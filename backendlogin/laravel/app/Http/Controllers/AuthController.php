<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }

    public function register(Request $request)
{
    Log::info("Registration request: " . json_encode($request->all()));

    try {
        DB::beginTransaction();

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Log::info("User created successfully: " . $user->id);

        // Create profile via HTTP with proper error handling
        $profileData = [
            'user_id' => $user->id, // belangrijk: koppel aan user
            'first_name' => $request->name,
            'last_name' => $request->name,
            'email' => $request->email,
        ];

        $response = Http::timeout(30)->post('http://nginx/api/sync-profile', $profileData);

        if ($response->successful()) {
            $profileResponse = $response->json();
            Log::info("Profile created successfully: " . json_encode($profileResponse));
        } else {
            Log::error("Profile creation failed: " . $response->body());
            throw new \Exception('Profile creation failed: ' . $response->body());
        }

        $response = Http::timeout(30)->post('http://nginx/api/sync-calendar', $profileData);

        if ($response->successful()) {
            $profileResponse = $response->json();
            Log::info("Profile created successfully: " . json_encode($profileResponse));
        } else {
            Log::error("Profile creation failed: " . $response->body());
            throw new \Exception('Profile creation failed: ' . $response->body());
        }

        $token = JWTAuth::fromUser($user);
        $refreshToken = $this->createRefreshToken($user->id);

        DB::commit();

        return $this->respondWithToken($token, $refreshToken, 'User successfully registered');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error creating user: " . $e->getMessage());
        return response()->json([
            'error' => 'User registration failed',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        //JWTAuth::factory()->setTTL(config('jwt.ttl'));

        $user = Auth::user();

        $customClaims = [
            //'role' => $user->role
            'name' => $user->name,
            'email' => $user->email,
            'id' => $user->id,
        ];

        $token = JWTAuth::claims($customClaims)->fromUser($user);

        $refreshToken = $this->createRefreshToken($user->id);
        Log::info('token', ['token' => $token]);
        Log::info('refreshToken', ['refreshToken' => $refreshToken]);

        return $this->respondWithToken($token, $refreshToken, 'Login successful');
    }

    public function me()
    {
        $response = response()->json(auth()->user());
        Log::info("me response: " . json_encode($response));
        return response()->json(auth()->user());
    }

    public function logout()
    {
        //auth()->logout();
        Log::info('Attempting to logout');

        //Delete refreshtoken out of database and cookie
        DB::table('refresh_tokens')
            ->where('user_id', auth()->user()->id)
            ->delete();

        return response()->json(['message' => 'Successfully logged out'])
            ->cookie(cookie()->forget('token'))
            ->cookie(cookie()->forget('refresh_token'));
    }

    public function refresh(Request $request)
    {
        Log::info('refresh request', ['request' => $request->all()]);
        Log::info('Attempting to refresh token');
        $refreshToken = $request->cookie('refresh_token');
        Log::info('Refresh token: ' . $refreshToken);
        if (!$refreshToken) {
            Log::info('Refresh token missing');
            return response()->json(['error' => 'Refresh token missing'], 401);
        }

        // Hashen en zoeken in DB
        $hashed = hash('sha256', $refreshToken);
        $record = DB::table('refresh_tokens')->where('token', $hashed)->first();

        if (!$record || now()->greaterThan($record->expires_at)) {
            return response()->json(['error' => 'Invalid or expired refresh token'], 401);
        }

        Log::info('Refresh token valid for user_id: ' . $record->user_id);

        // Nieuwe JWT en nieuw refresh token
        $user = User::find($record->user_id);
        $customClaims = [
            //'role' => $user->role
            'name' => $user->name,
            'email' => $user->email,
            'id' => $user->id,
        ];

        $newAccessToken = JWTAuth::claims($customClaims)->fromUser($user);
        //$newAccessToken = JWTAuth::fromUser($user);
        $newRefreshToken = $this->createRefreshToken($user->id);

        // Oude refresh token intrekken
        DB::table('refresh_tokens')->where('token', $hashed)->delete();

        return $this->respondWithToken($newAccessToken, $newRefreshToken, 'Token refreshed successfully');
    }

    protected function respondWithToken($token, $refreshToken, $message)
{
    Log::info('🔐 Setting cookies:', [
        'token_ttl' => config('jwt.ttl'),
        'refresh_ttl' => config('jwt.refresh_ttl'),
        'domain' => config('session.domain'),
        'secure' => config('session.secure'),
        'same_site' => config('session.same_site'),
    ]);

    return response()->json([
        'message' => $message,
        'user' => auth()->user(),
    ])
    ->cookie('token', $token, config('jwt.ttl'), '/', null, true, true)
    ->cookie('refresh_token', $refreshToken, config('jwt.refresh_ttl'), '/', null, true, true);
}

    private function createRefreshToken($userId)
    {
        //delete oude tokens van deze user
        DB::table('refresh_tokens')->where('user_id', $userId)->delete();

        $plainToken = bin2hex(random_bytes(64)); // random string
        $hashedToken = hash('sha256', $plainToken);

        // Sla de hashed token op in de DB
        DB::table('refresh_tokens')->insert([
            'user_id' => $userId,
            'token' => $hashedToken,
            'expires_at' => now()->addDays(1), // TTL bv. 30 dagen
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $plainToken; // deze stuur je mee als cookie
    }

    public function user(Request $request)
    {
        Log::info('[/auth/user] User request received');
        Log::info('[/auth/user] Authenticated user: ' . json_encode(auth()->user()));

        return response()->json(auth()->user());
    }

    public function determineCompanyType($companyType)
    {
        // Zoek in Wholesale
        if ($companyType === 1 ) {
            return 2; // Wholesale
        }

        // Zoek in Reseller
        if ($companyType === 2 ) {
            return 3; // Reseller
        }   

        // Zoek in Customer
        if ($companyType === 3 ) {
            return 4; // Customer
        }

        throw new \Exception("Company ID not found in any company type: " . $companyType);
    }

}