<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php', // Zorg dat api routes bestaan
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'cors' => \App\Http\Middleware\Cors::class,
            'jwt.validate' => \App\Http\Middleware\JwtValidateOnly::class,

        ]);
        
        // Voeg CORS middleware toe aan api group
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Cors::class,
        ]);

         // Zorg dat API auth niet redirect naar login route
        $middleware->redirectGuestsTo(fn () => null);
        $middleware->redirectUsersTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();