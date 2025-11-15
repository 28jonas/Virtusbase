<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

use App\Models\Wholesale;
use App\Models\Reseller;
use App\Models\Customer;

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
        Log::info('🔁 Main backend: /auth/login called', ['request_data' => $request->all()]);
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

        $user = Auth::user();

        // Check if 2FA is enabled
        if ($user->two_factor_enabled) {
            // If no 2FA code provided, require verification
            if (!$request->has('two_factor_code') && !$request->has('recovery_code')) {

                // Generate email code if method is email
                if ($user->two_factor_method === 'email') {
                    $this->generateAndSend2FACode($user);
                }

                return response()->json([
                    'message' => '2FA verification required',
                    'requires_2fa' => true,
                    'two_factor_method' => $user->two_factor_method
                ], 200);
            }

            // Verify 2FA code based on method
            if ($request->has('two_factor_code')) {
                if ($user->two_factor_method === 'google2FA') {
                    // 🔥 GOOGLE 2FA VERIFICATIE
                    $isValid = $this->verifyGoogle2FACode($user, $request->two_factor_code);
                } else {
                    // 🔥 EMAIL 2FA VERIFICATIE  
                    $isValid = $this->verifyEmail2FACode($user, $request->two_factor_code);

                    // Clear used email code only if valid
                    if ($isValid) {
                        $user->update([
                            'two_factor_code' => null,
                            'two_factor_expires_at' => null
                        ]);
                    }
                }

                if (!$isValid) {
                    return response()->json(['error' => 'Invalid 2FA code'], 401);
                }
            }
        }
        Log::info('[/auth/login] User: ' . json_encode($user));
        // Continue with token generation...
        $customClaims = [
            'name' => $user->name,
            'email' => $user->email,
            'id' => $user->id,
        ];

        $token = JWTAuth::claims($customClaims)->fromUser($user);
        $refreshToken = $this->createRefreshToken($user->id);

        return $this->respondWithToken($token, $refreshToken, 'Login successful');
    }

    public function me()
    {
        $response = response()->json(auth()->user());
        return response()->json(auth()->user());
    }

    public function meInfo()
    {
        $user = auth()->user();
        return response()->json($user);
    }

    public function logout()
    {
        // Log::info('Attempting to logout');

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
            'name' => $user->name,
            'email' => $user->email,
            'id' => $user->id,
        ];

        $newAccessToken = JWTAuth::claims($customClaims)->fromUser($user);
        $newRefreshToken = $this->createRefreshToken($user->id);

        // Oude refresh token intrekken
        DB::table('refresh_tokens')->where('token', $hashed)->delete();

        return $this->respondWithToken($newAccessToken, $newRefreshToken, 'Token refreshed successfully');
    }

    protected function respondWithToken($token, $refreshToken, $message)
    {
        Log::info("🔐 respondWithToken called");

        // Gebruik localhost voor development
        //$cookieDomain = 'localhost'; // ← Probeer dit eerst

        // OF als je via IP adres werkt:
        $cookieDomain = '192.168.0.126';

        // OF: dynamic maar fallback naar localhost
        $origin = request()->header('origin');
        if ($origin) {
            $parsedOrigin = parse_url($origin);
            $cookieDomain = $parsedOrigin['host'] ?? 'localhost';

            // Als het een IP adres is, gebruik dan null (meest compatibel)
            if (filter_var($cookieDomain, FILTER_VALIDATE_IP)) {
                $cookieDomain = null; // ← IP adressen werken beter met null
            }
        }

        Log::info('🔐 Setting cookies for domain:', ['domain' => $cookieDomain]);

        $tokenCookie = cookie(
            'token',
            $token,
            config('jwt.ttl'), // of 60 * 24 * 7 voor 1 week
            '/',
            $cookieDomain, // null voor IP adressen
            false, // secure: false voor HTTP development
            true,  // httpOnly: true
            false,
            'lax' // sameSite
        );

        $refreshTokenCookie = cookie(
            'refresh_token',
            $refreshToken,
            config('jwt.refresh_ttl'), // of 60 * 24 * 30 voor 30 dagen
            '/',
            $cookieDomain, // null voor IP adressen
            false,
            true,
            false,
            'lax'
        );

        return response()->json([
            'message' => $message,
            'user' => auth()->user(),
        ])->withCookie($tokenCookie)
            ->withCookie($refreshTokenCookie);
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
        Log::info('Refresh token created for user_id: ' . $userId);
        return $plainToken; // deze stuur je mee als cookie
    }

    public function user(Request $request)
    {
        Log::info('[/auth/user] User request received');
        Log::info('[/auth/user] Authenticated user: ' . json_encode(auth()->user()));

        return response()->json(auth()->user());
    }

    /**
     * Forgot Password - Send reset link
     */
    public function forgotPassword(Request $request)
    {
        // Log::info('[/auth/forgot-password] Forgot password request received');
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            // Log::info('response: ' . json_encode($validator->errors()));
            return response()->json([
                'error' => 'Invalid email address or user not found'
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            // Generate reset token
            $token = Str::random(64);

            // Store token in database (you might want to create a password_resets table)
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Send email
            $this->sendPasswordResetEmail($user, $token);

            return response()->json([
                'message' => 'Password reset link has been sent to your email'
            ]);

        } catch (\Exception $e) {
            Log::error('Forgot password error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to send reset link. Please try again.'
            ], 500);
        }
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request)
    {
        // Log::info('[/auth/reset-password] Reset password request received');
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
                return response()->json([
                    'error' => 'Invalid or expired reset token'
                ], 400);
            }

            // Check if token is expired (1 hour validity)
            if (now()->diffInMinutes($resetRecord->created_at) > 60) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'error' => 'Reset token has expired'
                ], 400);
            }

            // Update user password
            $user = User::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Delete used reset token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Send confirmation email
            $this->sendPasswordChangedConfirmation($user);

            return response()->json([
                'message' => 'Password has been reset successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to reset password. Please try again.'
            ], 500);
        }
    }

    /**
     * Enable/Disable 2FA
     */
    public function toggleTwoFactor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enable' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();

        $user->update([
            'two_factor_enabled' => $request->enable,
            'two_factor_code' => null,
            'two_factor_expires_at' => null
        ]);

        return response()->json([
            'message' => $request->enable ? '2FA has been enabled' : '2FA has been disabled',
            'two_factor_enabled' => $user->two_factor_enabled
        ]);
    }

    /**
     * Verify 2FA Code (for login)
     */
    /**
     * Verify 2FA Code (for login) - Unified method
     */
    public function verifyTwoFactor(Request $request)
    {
        // Log::info('[/auth/verify-two-factor] Verify 2FA code request received');
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'two_factor_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Log::info('[/auth/verify-two-factor] User: ' . json_encode($user));

        // Bepaal verificatie methode op basis van user's 2FA settings
        if ($user->two_factor_method === 'google2FA') {
            // Google Authenticator verificatie
            $isValid = $this->verifyGoogle2FACode($user, $request->two_factor_code);
        } else {
            // Email verificatie
            $isValid = $this->verifyEmail2FACode($user, $request->two_factor_code);

            // Clear used email code alleen als valid
            if ($isValid) {
                $user->update([
                    'two_factor_code' => null,
                    'two_factor_expires_at' => null
                ]);
            }
        }

        if (!$isValid) {
            return response()->json(['error' => 'Invalid 2FA code'], 401);
        }

        // Log::info("2FA verification successful for user: {$user->email}");

        $customClaims = [
            'name' => $user->name,
            'email' => $user->email,
            'id' => $user->id,
        ];

        Auth::login($user);
        $token = JWTAuth::claims($customClaims)->fromUser($user);
        $refreshToken = $this->createRefreshToken($user->id);
        // Log::info('refreshtoken created', ['refresh_token' => $refreshToken]);
        return $this->respondWithToken($token, $refreshToken, '2FA verification successful');
    }

    /**
     * Generate and send 2FA code
     */
    private function generateAndSend2FACode($user)
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10); // Code valid for 10 minutes

        $user->update([
            'two_factor_code' => Hash::make($code),
            'two_factor_expires_at' => $expiresAt
        ]);

        // Send 2FA code via email
        $this->send2FACodeEmail($user, $code);

        // Log::info("2FA code generated for user: {$user->email}");
    }

    /**
     * Verify 2FA code
     */
    /**
     * Verify Google Authenticator 2FA code
     */
    private function verifyGoogle2FACode($user, $code)
    {
        try {
            // Log::info("Verifying Google 2FA code for user: {$user->email}");

            if (empty($user->two_factor_secret)) {
                Log::error("No Google 2FA secret found for user: {$user->email}");
                return false;
            }

            $secret = decrypt($user->two_factor_secret);
            $google2fa = app('pragmarx.google2fa');
            $isValid = $google2fa->verifyKey($secret, $code);

            // Log::info("Google 2FA verification result: " . ($isValid ? 'VALID' : 'INVALID'));
            return $isValid;

        } catch (\Exception $e) {
            Log::error("Google 2FA verification error for user {$user->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify Email 2FA code (hernoem de oude functie)
     */
    private function verifyEmail2FACode($user, $code)
    {
        // Log::info("Verifying Email 2FA code for user: {$user->email}");

        if (!$user->two_factor_code || !$user->two_factor_expires_at) {
            // Log::info("No Email 2FA code found for user: {$user->email}");
            return false;
        }

        if (now()->greaterThan($user->two_factor_expires_at)) {
            // Log::info("Email 2FA code expired for user: {$user->email}");
            return false;
        }

        return Hash::check($code, $user->two_factor_code);
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($user, $token)
    {
        $resetUrl = config('app.frontend_url') . "/reset-password?token={$token}&email=" . urlencode($user->email);

        Mail::send('emails.password-reset', [
            'user' => $user,
            'resetUrl' => $resetUrl,
            'token' => $token
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Password Reset Request - ' . config('app.name'));
        });
    }

    /**
     * Send password changed confirmation
     */
    private function sendPasswordChangedConfirmation($user)
    {
        Mail::send('emails.password-changed', [
            'user' => $user
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Password Changed - ' . config('app.name'));
        });
    }

    /**
     * Send 2FA code email
     */
    private function send2FACodeEmail($user, $code)
    {
        Mail::send('emails.two-factor', [
            'user' => $user,
            'code' => $code
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your 2FA Verification Code - ' . config('app.name'));
        });
    }

    /**
     * Generate Authenticator secret and QR code
     */
    public function setupAuthenticator2FA(Request $request)
    {
        try {
            // Log::info('setupAuthenticator2FA request', ['request' => $request->all()]);
            $user = auth()->user();

            // Generate secret als er nog geen is
            if (!$user->two_factor_secret) {
                $google2fa = app('pragmarx.google2fa');
                $secret = $google2fa->generateSecretKey();

                $user->update([
                    'two_factor_secret' => encrypt($secret),
                    'two_factor_recovery_codes' => json_encode($this->generateRecoveryCodes()),
                ]);
            } else {
                $secret = decrypt($user->two_factor_secret);
            }

            // Generate QR code URL
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );

            // 🔥 Gebruik Google Charts API voor QR code (geen packages nodig)
            $googleChartsQrUrl = $this->generateGoogleChartsQRCode($qrCodeUrl);

            return response()->json([
                'secret' => $secret,
                'qr_code' => $googleChartsQrUrl, // URL naar Google Charts QR code
                'qr_code_url' => $qrCodeUrl, // Voor handmatige invoer
                'recovery_codes' => json_decode($user->two_factor_recovery_codes),
            ]);

        } catch (\Exception $e) {
            Log::error('2FA setup error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to setup 2FA'], 500);
        }
    }

    /**
     * Generate QR code using Google Charts API
     */
    private function generateGoogleChartsQRCode($data)
    {
        $size = 200;
        $encodedData = urlencode($data);

        // Google Charts QR code API
        return "https://chart.googleapis.com/chart?cht=qr&chs={$size}x{$size}&chl={$encodedData}";
    }

    /**
     * Verify and enable Authenticator 2FA
     */
    public function verifyAuthenticator2FA(Request $request)
    {
        // Log::info('verifyAuthenticator2FA request', ['request' => $request->all()]);
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            Log::error('verifyAuthenticator2FA validation error', ['errors' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        try {
            $user = auth()->user();
            // Log::info('verifyAuthenticator2FA user', ['user' => $user->toArray()]);
            $secret = decrypt($user->two_factor_secret);
            // Log::info('verifyAuthenticator2FA secret', ['secret' => $secret]);
            $google2fa = app('pragmarx.google2fa');
            $valid = $google2fa->verifyKey($secret, $request->code);
            // Log::info('verifyAuthenticator2FA valid', ['valid' => $valid]);
            if (!$valid) {
                return response()->json(['error' => 'Invalid verification code'], 422);
            }

            // Enable Authenticator 2FA
            $user->update([
                'two_factor_enabled' => true,
                'two_factor_method' => 'google2FA',
            ]);

            return response()->json([
                'message' => 'Authenticator 2FA enabled successfully',
                'two_factor_enabled' => true,
                'two_factor_method' => 'google2FA'
            ]);

        } catch (\Exception $e) {
            Log::error('2FA verification error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to verify 2FA'], 500);
        }
    }

    /**
     * Disable 2FA
     */
    public function disable2FA(Request $request)
    {
        try {
            $user = auth()->user();

            $user->update([
                'two_factor_enabled' => false,
                'two_factor_method' => null,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
            ]);

            return response()->json([
                'message' => '2FA disabled successfully',
                'two_factor_enabled' => false
            ]);

        } catch (\Exception $e) {
            Log::error('2FA disable error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to disable 2FA'], 500);
        }
    }

    /**
     * Verify 2FA with recovery code
     */
    public function verifyRecoveryCode(Request $request)
    {
        // Log::info('verifyRecoveryCode request', ['request' => $request->all()]);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'recovery_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            // Log::info('verifyRecoveryCode validator', ['validator' => $validator->errors()]);
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();
        // Log::info('verifyRecoveryCode user', ['user' => $user->toArray()]);
        if (!$user || !$user->two_factor_recovery_codes) {
            return response()->json(['error' => 'Invalid recovery code'], 401);
        }

        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        $codeIndex = array_search($request->recovery_code, $recoveryCodes);
        // Log::info('verifyRecoveryCode codeIndex', ['codeIndex' => $codeIndex]);
        // Log::info('verifyRecoveryCode recoveryCodes', ['recoveryCodes' => $recoveryCodes]);
        if ($codeIndex === false) {
            // Log::info('verifyRecoveryCode codeIndex', ['codeIndex' => $codeIndex]);
            return response()->json(['error' => 'Invalid recovery code'], 401);
        }

        // Remove used recovery code
        unset($recoveryCodes[$codeIndex]);
        $user->update([
            'two_factor_recovery_codes' => json_encode(array_values($recoveryCodes)),
        ]);

        // Continue with login
        Auth::login($user);

        $customClaims = [
            'name' => $user->name,
            'email' => $user->email,
            'type' => $user->type,
            'user_role_company' => $user->user_role_company,
            'user_id' => $user->id,
            'parent_id' => $user->parent_company_id,
            'parent_name' => $user->parent_name,
            'parent_company_name' => $user->parent_company_name,
            'company_name' => $user->company_name,
            'company_id' => $user->company_id,
            'company_type' => $user->company_type,
        ];
        $token = JWTAuth::claims($customClaims)->fromUser($user);
        $refreshToken = $this->createRefreshToken($user->id);

        return $this->respondWithToken($token, $refreshToken, 'Login with recovery code successful');
    }

    /**
     * Generate recovery codes
     */
    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(Str::random(10) . '-' . Str::random(10));
        }
        return $codes;
    }
}