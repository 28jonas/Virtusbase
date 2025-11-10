<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->cookie('token');
            
            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            return $next($request);

        } catch (TokenExpiredException $e) {
            // Token is expired, try to refresh
            return $this->handleExpiredToken($request, $next);
            
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token absent'], 401);
        }
    }

    //Kijkzn of,ik deze moet aanpassen/wordt dit nog gebruikt??
    protected function handleExpiredToken($request, $next)
    {
        try {
            $refreshToken = $request->cookie('refresh_token');
            
            if (!$refreshToken) {
                return response()->json(['error' => 'Refresh token not found'], 401);
            }

            // Verify refresh token
            JWTAuth::setToken($refreshToken);
            $payload = JWTAuth::getPayload();
            
            if (!$payload->get('refresh')) {
                return response()->json(['error' => 'Invalid refresh token'], 401);
            }

            // Refresh both tokens
            $newToken = JWTAuth::refresh($refreshToken);
            $newRefreshToken = JWTAuth::claims(['refresh' => true])
                ->setTTL(config('jwt.refresh_ttl'))
                ->refresh($refreshToken);

            // Continue with the request and set new cookies
            $response = $next($request);
            
            return $response->cookie('token', $newToken, config('jwt.ttl'), '/', null, true, true)
                ->cookie('refresh_token', $newRefreshToken, config('jwt.refresh_ttl'), '/', null, true, true);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not refresh token'], 401);
        }
    }
}