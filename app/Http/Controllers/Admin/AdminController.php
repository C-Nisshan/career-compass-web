<?php

namespace App\Http\Controllers\Admin;

use App\Mail\RegistrationStatus;
use App\Models\User;
use App\Models\VerificationDocument;
use App\Models\VerificationWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    
    public function pendingRegistrations(Request $request)
    {
        $users = User::where('status', 'pending')
            ->with(['profile', 'verificationDocuments.verificationWorkflow'])
            ->paginate(10);

        return response()->json($users);
    }

    public function reviewRegistration(Request $request, $uuid)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string|max:255',
        ]);

        return DB::transaction(function () use ($request, $uuid) {
            $user = User::with('profile', 'verificationDocuments.verificationWorkflow')->findOrFail($uuid);
            $action = $request->action;
            $status = $action === 'approve' ? 'approved' : 'rejected';

            foreach ($user->verificationDocuments as $document) {
                $workflow = $document->verificationWorkflow;
                if (!$workflow) {
                    throw new \Exception(__('Workflow not found for document :id', ['id' => $document->uuid]));
                }

                $document->verified_status = $status;
                if ($action === 'approve') {
                    $document->verified_at = now();
                } else {
                    $workflow->rejection_reason = $request->rejection_reason; // Store reason in workflow
                }
                $workflow->status = $status;
                $workflow->approved_by = Auth::user()->uuid;

                $document->save();
                $workflow->save();
            }

            $user->status = $status;
            if ($user->profile) {
                $user->profile->verified_status = $status;
                $user->profile->save();
            } else {
                throw new \Exception(__('Profile not found for user :id', ['id' => $user->uuid]));
            }
            $user->save();

            Mail::to($user->email)->queue(new RegistrationStatus($user, $status));

            return response()->json(['message' => __('Registration processed successfully')]);
        });
    }
}