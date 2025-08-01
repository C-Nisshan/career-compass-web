<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    /**
     * Handle the contact form submission via API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Attempt to get authenticated user without enforcing middleware
        $user = null;
        try {
            $token = $request->cookie('token');
            if ($token) {
                JWTAuth::setToken($token);
                $user = JWTAuth::authenticate();
            }
        } catch (\Exception $e) {
            // No user authenticated, proceed without user_id
            Log::info('No authenticated user for contact form submission', [
                'error' => $e->getMessage()
            ]);
        }

        $contactMessage = ContactMessage::create([
            'uuid' => Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'user_id' => $user ? $user->uuid : null,
        ]);

        try {
            Mail::to($validated['email'])->send(new ContactFormSubmitted($contactMessage));
        } catch (\Exception $e) {
            Log::error('Failed to send contact form email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Your message has been sent successfully!'
        ], 200);
    }
}