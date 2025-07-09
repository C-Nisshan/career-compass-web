<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class JwtFromCookieMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Log all cookies and headers for debugging
            Log::info('Cookies received in middleware', [
                'cookies' => $request->cookies->all(),
                'cookie_token' => $request->cookie('token'),
                'raw_cookie_header' => $request->header('Cookie', 'none'),
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method(),
            ]);

            // Try to retrieve the token from the cookie
            $token = $request->cookie('token');

            // Fallback: Extract token from raw Cookie header if $request->cookie('token') is null
            if (!$token) {
                $cookieHeader = $request->header('Cookie', '');
                if ($cookieHeader) {
                    // Parse the Cookie header
                    $cookies = array_reduce(explode('; ', $cookieHeader), function ($carry, $cookie) {
                        [$name, $value] = explode('=', $cookie, 2) + [null, null];
                        $carry[$name] = $value;
                        return $carry;
                    }, []);

                    $token = $cookies['token'] ?? null;

                    Log::info('Attempted to extract token from Cookie header', [
                        'cookie_header' => $cookieHeader,
                        'extracted_token' => $token ? (substr($token, 0, 10) . '...') : 'none',
                    ]);
                }
            }

            if (!$token) {
                Log::error('No token found in cookie or header', [
                    'cookies' => $request->cookies->all(),
                    'raw_cookie_header' => $request->header('Cookie', 'none'),
                    'request_url' => $request->fullUrl(),
                ]);
                //return response()->json(['error' => 'Authentication token not found'], 401);
                return redirect()->route('login');
            }

            // Log the token for debugging (partial for security)
            Log::info('Token retrieved', [
                'token_prefix' => substr($token, 0, 10) . '...',
                'token_length' => strlen($token),
            ]);

            // Set the token for JWTAuth
            JWTAuth::setToken($token);

            // Authenticate the token
            $user = JWTAuth::authenticate();

            if (!$user) {
                Log::error('User not found for token', [
                    'token_prefix' => substr($token, 0, 10) . '...',
                ]);
                return response()->json(['error' => 'Invalid token'], 401);
            }

            // Verify token version
            $payload = JWTAuth::getPayload();
            Log::info('Payload Token version: ' . $payload->get('token_version'));
            Log::info('DB Token version: ' . $user->token_version);

            if ($payload->get('token_version') !== $user->token_version) {
                Log::error('Token version mismatch', [
                    'payload_version' => $payload->get('token_version'),
                    'user_version' => $user->token_version,
                ]);
                return response()->json(['error' => 'Token has been invalidated'], 401);
            }

            // Set the authenticated user for the request
            auth()->setUser($user);

            return $next($request);
        } catch (JWTException $e) {
            Log::error('JWT Exception: ' . $e->getMessage(), [
                'request_url' => $request->fullUrl(),
                'token_prefix' => isset($token) ? substr($token, 0, 10) . '...' : 'none',
            ]);
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (\Exception $e) {
            Log::error('Unexpected error in JwtFromCookieMiddleware: ' . $e->getMessage(), [
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Authentication error'], 401);
        }
    }
}