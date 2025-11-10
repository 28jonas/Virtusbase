<?php

namespace App\Http\Middleware;

use Closure;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class JwtValidateOnly
{
    public function handle($request, Closure $next)
    {
        Log::info('[JwtValidateOnly] Middleware called for: ' . $request->path());
        
        try {
            // Parse token ZONDER database lookup
            $token = $request->bearerToken();
            Log::debug('[JwtValidateOnly] Bearer token: ' . ($token ? 'present' : 'missing'));
            
            if (!$token) {
                // Probeer cookie als fallback
                $token = $request->cookie('token');
                Log::debug('[JwtValidateOnly] Cookie token: ' . ($token ? 'present' : 'missing'));
            }
            
            if (!$token) {
                Log::warning('[JwtValidateOnly] No token found in request');
                return response()->json(['error' => 'Token absent'], 401);
            }
            
            $payload = JWTAuth::setToken($token)->getPayload();
            
            Log::info('[JwtValidateOnly] Token validated for user:', [
                'user_id' => $payload->get('sub'),
                'user_name' => $payload->get('name')
            ]);
            
            // Voeg user data toe aan request vanuit JWT payload
            $request->merge([
                'user' => [
                    'id' => $payload->get('sub'),
                    'name' => $payload->get('name'),
                    'email' => $payload->get('email'),
                    'type' => $payload->get('type')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('[JwtValidateOnly] Token validation failed:', [
                'error' => $e->getMessage(),
                'path' => $request->path()
            ]);
            return response()->json(['error' => 'Invalid token: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}