<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class NewsletterController extends Controller
{
    /**
     * Handle the newsletter subscription via API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:newsletter_subscriptions,email',
        ]);

        // Attempt to get authenticated user without enforcing middleware
        $user = null;
        try {
            $token = $request->cookie('token');
            if ($token) {
                JWTAuth::setToken($token);
                $user = JWTAuth::authenticate();
                Log::info('Authenticated user for newsletter subscription', [
                    'user_uuid' => $user ? $user->uuid : 'none',
                    'email' => $validated['email']
                ]);
            } else {
                Log::info('No token provided for newsletter subscription');
            }
        } catch (\Exception $e) {
            Log::info('No authenticated user for newsletter subscription', [
                'error' => $e->getMessage(),
                'email' => $validated['email']
            ]);
        }

        $subscription = NewsletterSubscription::create([
            'uuid' => Str::uuid(),
            'email' => $validated['email'],
            'user_id' => $user ? $user->uuid : null,
            'subscribed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Thank you for subscribing to our newsletter!'
        ], 200);
    }
}