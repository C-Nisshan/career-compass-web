<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages(['email' => 'Email not found.']);
        }

        // Generate 6-digit OTP
        $otp = sprintf('%06d', mt_rand(0, 999999));
        $expiresAt = now()->addMinutes(10);

        // Delete old OTPs
        PasswordResetOtp::where('email', $request->email)->delete();

        // Store new OTP
        try {
            $otpRecord = PasswordResetOtp::create([
                'email' => $request->email,
                'otp' => $otp,
                'expires_at' => $expiresAt,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store OTP', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
            throw ValidationException::withMessages(['email' => 'Failed to generate OTP.']);
        }

        try {
            // Send OTP email
            Mail::raw("Your OTP for password reset is: $otp\nValid for 10 minutes.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Career Compass - Password Reset OTP');
            });

            Log::info('OTP sent successfully', ['email' => $request->email, 'otp' => $otp, 'uuid' => $otpRecord->uuid]);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
            throw ValidationException::withMessages(['email' => 'Failed to send OTP. Please try again later.']);
        }

        return response()->json(['message' => 'OTP sent to your email.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)
                                    ->where('otp', $request->otp)
                                    ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired OTP.']);
        }

        return response()->json(['message' => 'OTP verified successfully.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)
                                    ->where('otp', $request->otp)
                                    ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired OTP.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages(['email' => 'Email not found.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete OTP
        PasswordResetOtp::where('email', $request->email)->delete();

        Log::info('Password reset successful', ['email' => $request->email]);

        return response()->json(['message' => 'Password reset successfully.', 'redirect' => route('login')]);
    }
}