<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        $allowedOrigins = [
            'http://localhost:5173',
            'http://localhost:3000',
            'http://localhost:8080' // voeg andere origins toe indien nodig
        ];

        $origin = $request->header('Origin');
        
        if (in_array($origin, $allowedOrigins)) {
            $response = $next($request);
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            
            return $response;
        }

        // Voor preflight requests (OPTIONS)
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
            $response->headers->set('Access-Control-Allow-Origin', $allowedOrigins[0]); // of dynamisch
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            return $response;
        }

        return $next($request);
    }
}