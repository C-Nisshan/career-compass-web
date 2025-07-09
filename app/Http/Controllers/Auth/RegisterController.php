<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDocumentUpload;
use App\Mail\RegistrationStatus;
use App\Models\User;
use App\Models\Profile;
use App\Models\VerificationDocument;
use App\Models\VerificationWorkflow;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:booker,provider',
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'pending',
                'is_active' => true,
            ]);

            Profile::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->uuid,
                'verified_status' => 'pending',
                'completion_step' => 'none',
            ]);

            Mail::to($user->email)->queue(new RegistrationStatus($user, 'pending'));

            return response()->json([
                'message' => __('Registration successful. Please log in to complete your profile.'),
                'redirect' => route('login'),
            ], 201);
        });
    }

    public function registerStudent(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|regex:/^[\+]?[0-9]{10,12}$/',
            'password' => 'required|string|min:8|confirmed',
            'nic_number' => ['required', 'regex:/^(\d{9}[VvXx]|\d{12})$/'],
            'nic_front' => 'required|file|mimes:jpeg,png,pdf|max:5120',
            'nic_back' => 'required_if:upload_type,photo|file|mimes:jpeg,png|max:5120',
            'upload_type' => 'required|in:photo,pdf',
        ]);

        return $this->processRegistration($request, 'booker');
    }

    public function registerProvider(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|regex:/^[\+]?[0-9]{10,12}$/',
            'password' => 'required|string|min:8|confirmed',
            'nic_number' => ['required', 'regex:/^(\d{9}[VvXx]|\d{12})$/'],
            'nic_front' => 'required|file|mimes:jpeg,png,pdf|max:5120',
            'nic_back' => 'required_if:upload_type,photo|file|mimes:jpeg,png|max:5120',
            'land_deed' => 'required|file|mimes:jpeg,png,pdf|max:10240',
            'upload_type' => 'required|in:photo,pdf',
        ]);

        return $this->processRegistration($request, 'provider');
    }

    protected function processRegistration(Request $request, string $role)
    {
        return DB::transaction(function () use ($request, $role) {
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role,
                'status' => 'pending',
            ]);

            Profile::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->uuid,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'nic_number' => $request->nic_number,
                'verified_status' => 'pending',
            ]);

            $documents = [];
            $uploadType = $request->upload_type;
            $format = $uploadType === 'pdf' ? 'pdf' : 'photo';

            if ($request->hasFile('nic_front')) {
                $path = $this->storeAndValidateFile($request->file('nic_front'), $user->uuid, 'nic_front', $format);
                $doc = VerificationDocument::create([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $user->uuid,
                    'document_type' => 'nic',
                    'file_path' => $path,
                    'verified_status' => 'pending',
                ]);
                VerificationWorkflow::create([
                    'uuid' => (string) Str::uuid(),
                    'verification_document_id' => $doc->uuid,
                    'status' => 'pending',
                ]);
                $documents[] = $doc;
            }

            if ($request->hasFile('nic_back') && $uploadType === 'photo') {
                $path = $this->storeAndValidateFile($request->file('nic_back'), $user->uuid, 'nic_back', 'photo');
                $doc = VerificationDocument::create([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $user->uuid,
                    'document_type' => 'nic',
                    'file_path' => $path,
                    'verified_status' => 'pending',
                ]);
                VerificationWorkflow::create([
                    'uuid' => (string) Str::uuid(),
                    'verification_document_id' => $doc->uuid,
                    'status' => 'pending',
                ]);
                $documents[] = $doc;
            }

            if ($role === 'provider' && $request->hasFile('land_deed')) {
                $path = $this->storeAndValidateFile($request->file('land_deed'), $user->uuid, 'land_deed', $format);
                $doc = VerificationDocument::create([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $user->uuid,
                    'document_type' => 'land_deed',
                    'file_path' => $path,
                    'verified_status' => 'pending',
                ]);
                VerificationWorkflow::create([
                    'uuid' => (string) Str::uuid(),
                    'verification_document_id' => $doc->uuid,
                    'status' => 'pending',
                ]);
                $documents[] = $doc;
            }

            ProcessDocumentUpload::dispatch($user);

            Mail::to($user->email)->queue(new RegistrationStatus($user, 'pending'));

            return response()->json([
                'message' => __('Registration submitted. Awaiting admin approval.')
            ], 201);
        });
    }

    protected function storeAndValidateFile($file, $userId, $type, $format)
    {
        $filename = "pending/{$userId}_{$type}_" . Str::random(10) . '.' . $file->extension();
        $path = $file->storeAs('', $filename, 'local');

        if ($format === 'photo') {
            $image = Image::make(Storage::disk('local')->path($path));
            if ($image->width() < 800 || $image->height() < 600) {
                Storage::disk('local')->delete($path);
                throw new \Exception(__("{$type} photo resolution too low. Minimum 800x600 required."));
            }
            $laplacianVariance = $this->calculateLaplacianVariance($image);
            if ($laplacianVariance < 100) {
                Storage::disk('local')->delete($path);
                throw new \Exception(__("{$type} photo is too blurry. Try better lighting."));
            }
        }

        return $path;
    }

    protected function calculateLaplacianVariance($image)
    {
        $grayImage = $image->greyscale();
        $pixels = [];
        for ($y = 1; $y < $image->height() - 1; $y++) {
            for ($x = 1; $x < $image->width() - 1; $x++) {
                $laplacian = -4 * $grayImage->pickColor($x, $y)[0] +
                             $grayImage->pickColor($x-1, $y)[0] +
                             $grayImage->pickColor($x+1, $y)[0] +
                             $grayImage->pickColor($x, $y-1)[0] +
                             $grayImage->pickColor($x, $y+1)[0];
                $pixels[] = $laplacian;
            }
        }
        $mean = array_sum($pixels) / count($pixels);
        $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $pixels)) / count($pixels);
        return $variance;
    }
}