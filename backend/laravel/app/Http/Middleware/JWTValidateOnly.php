<?php

namespace App\Http\Middleware;

use Closure;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class JwtValidateOnly
{
    public function handle($request, Closure $next)
    {
        Log::info('🔐 [JwtValidateOnly] Middleware called for: ' . $request->path());
        
        // Log ALLE cookies die binnenkomen
        $allCookies = $request->cookies->all();
        Log::info('🍪 [JwtValidateOnly] All incoming cookies:', $allCookies);
        
        // Log specifiek de token cookies
        Log::info('🎯 [JwtValidateOnly] Specific cookie check:', [
            'has_token_cookie' => $request->hasCookie('token'),
            'has_refresh_token_cookie' => $request->hasCookie('refresh_token'),
            'token_value_length' => $request->hasCookie('token') ? strlen($request->cookie('token')) : 0,
            'refresh_token_value_length' => $request->hasCookie('refresh_token') ? strlen($request->cookie('refresh_token')) : 0,
        ]);

        // Log request headers (voor Bearer token)
        $headers = $request->headers->all();
        Log::info('📨 [JwtValidateOnly] Request headers:', [
            'authorization' => $headers['authorization'] ?? 'missing',
            'origin' => $headers['origin'] ?? 'missing',
            'cookie_header' => $headers['cookie'] ?? 'missing'
        ]);
        
        try {
            // Parse token ZONDER database lookup
            $token = $request->bearerToken();
            Log::debug('[JwtValidateOnly] Bearer token: ' . ($token ? 'present (' . strlen($token) . ' chars)' : 'missing'));
            
            if (!$token) {
                // Probeer cookie als fallback
                $token = $request->cookie('token');
                Log::debug('[JwtValidateOnly] Cookie token: ' . ($token ? 'present (' . strlen($token) . ' chars)' : 'missing'));
                
                // Als we token via cookie hebben, log de eerste 10 chars voor verificatie
                if ($token) {
                    Log::debug('[JwtValidateOnly] Token preview: ' . substr($token, 0, 20) . '...');
                }
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