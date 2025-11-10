<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;


class StravaController extends Controller
{
    public function redirectToStrava()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

       /* $client = new Client([
            'verify' => false,
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]
        ]);*/

        // Genereer een unieke state
        $state = Str::random(40);

        // Sla de state op in de sessie EN in een cookie
        Session::put('strava_state', $state);
        Cookie::queue('strava_state', $state, 5); // 5 minuten geldig

        return Socialite::driver('strava')
            ->with(['state' => $state])
            ->scopes(['read_all', 'activity:read_all'])
            ->redirect();
    }

    public function handleStravaCallback(Request $request)
    {
        try {
            // Recreate HTTP client with SSL verification disabled
           /* $client = new Client([
                'verify' => false,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
                ]
            ]);*/


            // Basis validaties
            if ($request->has('error')) {
                return redirect('/sport')->with('error', 'Strava autorisatie geannuleerd.');
            }

            // Haal state uit zowel request als sessie/cookie
            $requestState = $request->state;
            $sessionState = Session::pull('strava_state');
            $cookieState = $request->cookie('strava_state');

            if (!$requestState || ($requestState !== $sessionState && $requestState !== $cookieState)) {
                Log::error('State mismatch', [
                    'request_state' => $requestState,
                    'session_state' => $sessionState,
                    'cookie_state' => $cookieState
                ]);
                throw new \Exception('Ongeldige state parameter');
            }

            // Verwijder de cookie
            Cookie::queue(Cookie::forget('strava_state'));

            // Haal de gebruiker op via Socialite
            $stravaUser = Socialite::driver('strava')
                ->stateless()
                ->user();

            // Debug info
            Log::debug('Strava user data', (array)$stravaUser);

            // Haal de ingelogde gebruiker op
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('Geen ingelogde gebruiker gevonden');
            }

            // Update gebruiker
            DB::transaction(function () use ($user, $stravaUser) {
                $user->forceFill([
                    'strava_id' => $stravaUser->id,
                    'strava_access_token' => $stravaUser->token,
                    'strava_refresh_token' => $stravaUser->refreshToken,
                    'strava_token_expires_at' => now()->addSeconds($stravaUser->expiresIn),
                ])->save();
            });

            return redirect('/sport')->with('success', 'Strava succesvol gekoppeld!');

        } catch (\Exception $e) {
            Log::error('Strava callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/sport')->with('error', 'Koppeling mislukt: '.$e->getMessage());
        }
    }

    /*public function disconnectStrava()
    {
        try {
            $user = Auth::user();

            DB::transaction(function () use ($user) {
                $user->forceFill([
                    'strava_id' => null,
                    'strava_access_token' => null,
                    'strava_refresh_token' => null,
                    'strava_token_expires_at' => null,
                ])->save();
            });

            // Clear any cached Strava data
            \Illuminate\Support\Facades\Cache::forget('strava_activity_' . $user->id);

            return redirect('/sport')->with('success', 'Strava ontkoppeld!');

        } catch (\Exception $e) {
            Log::error('Strava disconnect failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect('/sport')->with('error', 'Ontkoppeling mislukt.');
        }
    }*/
}