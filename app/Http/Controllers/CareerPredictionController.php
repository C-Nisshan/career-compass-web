<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\CareerPrediction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CareerPredictionController extends Controller
{
    public function predict(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'text' => 'required|string|min:10', // Basic validation to ensure meaningful input
        ]);

        // Attempt to get authenticated user without enforcing middleware
        $user = null;
        try {
            $token = $request->cookie('token');
            if ($token) {
                JWTAuth::setToken($token);
                $user = JWTAuth::authenticate();
                Log::info('Authenticated user for career prediction', [
                    'user_uuid' => $user ? $user->uuid : 'none',
                    'input_text' => $validated['text']
                ]);
            } else {
                Log::info('No token provided for career prediction');
            }
        } catch (\Exception $e) {
            Log::info('No authenticated user for career prediction', [
                'error' => $e->getMessage(),
                'input_text' => $validated['text']
            ]);
        }

        // Forward to Python API
        $response = Http::post('http://localhost:8001/predict', $validated);

        // Handle errors
        if ($response->failed()) {
            Log::error('Failed to get career predictions', [
                'input_text' => $validated['text'],
                'error' => $response->reason()
            ]);
            return response()->json(['error' => 'Failed to get career predictions. Please try again.'], 500);
        }

        $apiResponse = $response->json();

        // Store in career_predictions table
        CareerPrediction::create([
            'uuid' => Str::uuid(),
            'input_text' => $validated['text'],
            'recommendations' => json_encode($apiResponse), // Store API response as JSON
            'user_id' => $user ? $user->uuid : null,
            'predicted_at' => now(),
        ]);

        return response()->json($apiResponse, 200);
    }
}