<?php


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\API\FamilyController;
use App\Http\Controllers\API\CalendarController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\ShoppingListController;
use App\Http\Controllers\API\ShoppingItemController;
use App\Http\Controllers\API\TodoController;
use App\Http\Controllers\API\HabitController;
use App\Http\Controllers\API\BankingCardController;
use App\Http\Controllers\API\CardController;
use App\Http\Controllers\API\GoalController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\IncomeController;
use App\Http\Controllers\API\StatsController;
use App\Http\Controllers\API\ChartController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\StravaController;
use App\Http\Controllers\API\ActivityTrackingController;
use App\Http\Controllers\API\HealthGoalsController;
use App\Http\Controllers\API\HealthMetricsController;
use App\Http\Controllers\API\ScheduleWorkoutController;
use App\Http\Controllers\API\StartWorkoutController;
use App\Http\Controllers\API\MealTrackingController;
use App\Http\Controllers\API\FoodItemController;
use App\Http\Controllers\API\MealTypeController;


use App\Models\Profile;

// Alle normale API routes
Route::get('/data', function () {
    return response()->json(['message' => 'Data from main backend']);
});

// Public Strava routes
//Route::get('/strava/redirect', [StravaController::class, 'redirectToStrava']);
//Route::get('/strava/callback', [StravaController::class, 'handleStravaCallback']);


Route::post('/sync-profile', function (Request $request) {
    Log::info('Sync profile request data:', $request->all());
    Log::info('Syncing profile:', ['first_name' => $request->first_name]); // ✅ CORRECT: array

    // Gebruik de query builder i.p.v. Eloquent
    DB::table('profiles')->updateOrInsert(
        ['id' => $request->user_id],
        [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'updated_at' => now(),
            'created_at' => now()
        ]
    );

    return response()->json(['success' => true]);
});

Route::post('/sync-calendar', function (Request $request) {
    Log::info('Sync calendar request data:', $request->all());

    // Gebruik de query builder i.p.v. Eloquent
    DB::table('calendars')->updateOrInsert(
        [
            'name' => $request->first_name,
            'color' => $request->color ?? '#FFFFFF',
            'description' => $request->description ?? "Personal Calendar",
            'owner_type' => $request->owner_type ?? 'Personal',
            'owner_id' => $request->user_id,
            'is_public' => $request->is_public ?? false,
            'updated_at' => now(),
            'created_at' => now()
        ]
    );

    return response()->json(['success' => true]);
});

// Alle routes zijn beveiligd met auth:sanctum middleware
Route::middleware('jwt.validate')->group(function () {

    // Family Routes
    Route::apiResource('families', FamilyController::class)->except(['show']);
    Route::get('/families/{family}', [FamilyController::class, 'show']); // Deze toevoegen!
    Route::post('families/{family}/members', [FamilyController::class, 'addMember']);
    Route::delete('families/{family}/members/{member}', [FamilyController::class, 'removeMember']);

    // Calendar Routes
    Route::apiResource('calendars', CalendarController::class);

    // Event Routes
    Route::apiResource('events', EventController::class)->except(['store']);
    Route::post('calendars/{calendar}/events', [EventController::class, 'store']);
    Route::get('events', [EventController::class, 'index']);

    // Shopping List Routes
    Route::apiResource('shopping-lists', ShoppingListController::class);
    Route::post('shopping-lists/{shoppingList}/share', [ShoppingListController::class, 'share']);
    Route::delete('shopping-lists/{shoppingList}/share/{user}', [ShoppingListController::class, 'unshare']);

    // Shopping Item Routes
    Route::get('shopping-lists/{shoppingList}/items', [ShoppingItemController::class, 'index']);
    Route::post('shopping-lists/{shoppingList}/items', [ShoppingItemController::class, 'store']);
    Route::put('shopping-items/{shoppingItem}', [ShoppingItemController::class, 'update']);
    Route::delete('shopping-items/{shoppingItem}', [ShoppingItemController::class, 'destroy']);
    Route::patch('shopping-items/{shoppingItem}/toggle', [ShoppingItemController::class, 'toggleComplete']);

    // Todo Routes
    Route::get('/todos/stats', [TodoController::class, 'getStats']);

    Route::apiResource('todos', TodoController::class);
    Route::patch('/todos/{todo}/toggle-complete', [TodoController::class, 'toggleComplete']);
    Route::delete('/todos/bulk/completed', [TodoController::class, 'bulkDeleteCompleted']);

    //habits
    Route::get('/habits/stats', [HabitController::class, 'getStats']);
    Route::apiResource('habits', HabitController::class);
    Route::patch('/habits/{habit}/toggle-completion', [HabitController::class, 'toggleCompletion']);
    Route::post('/habits/{habit}/log-completion', [HabitController::class, 'logCompletion']);
    Route::get('/habits/week/data', [HabitController::class, 'getWeekData']);

    // Banking Card Routes
    Route::get('/cards/stats', [BankingCardController::class, 'getStats']);
    Route::get('/cards/banks', [BankingCardController::class, 'getBanks']);
    Route::apiResource('cards', BankingCardController::class);

    Route::post('/cards/transfer', [BankingCardController::class, 'transfer']);

    // Cards
    Route::get('/cards', [CardController::class, 'index']);
    Route::post('/cards', [CardController::class, 'store']);
    Route::post('/cards/transfer', [CardController::class, 'transfer']);

    // Expenses
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);

    // Incomes
    Route::get('/incomes', [IncomeController::class, 'index']);
    Route::post('/incomes', [IncomeController::class, 'store']);

    // Goals
    Route::get('/goals', [GoalController::class, 'index']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::put('/goals/{goal}', [GoalController::class, 'update']);

    // Charts
    Route::get('/chart/balance', [ChartController::class, 'balanceHistory']);

    // Misc
    Route::get('/banks', [CardController::class, 'banks']);
    Route::get('/categories', [CategoryController::class, 'index']);

    // Cards
    Route::get('/cards', [CardController::class, 'index']);
    Route::post('/cards', [CardController::class, 'store']);
    Route::post('/cards/transfer', [CardController::class, 'transfer']);
    Route::get('/banks', [CardController::class, 'banks']);

    // Expenses
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);

    // Incomes
    Route::get('/incomes', [IncomeController::class, 'index']);
    Route::post('/incomes', [IncomeController::class, 'store']);

    // Goals
    Route::get('/goals', [GoalController::class, 'index']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::put('/goals/{goal}', [GoalController::class, 'update']);

    // Charts
    Route::get('/chart/balance', [ChartController::class, 'balanceHistory']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);

    // Stats
    Route::get('/stats/income-expense', [StatsController::class, 'incomeExpenseStats']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

    // Activity Tracking
    Route::get('/activity-tracking', [ActivityTrackingController::class, 'index']);
    Route::post('/activity-tracking/select-date', [ActivityTrackingController::class, 'selectDate']);
    Route::post('/activity-tracking/previous-week', [ActivityTrackingController::class, 'previousWeek']);
    Route::post('/activity-tracking/next-week', [ActivityTrackingController::class, 'nextWeek']);

    // Health Goals
    Route::get('/health-goals', [HealthGoalsController::class, 'index']);
    Route::post('/health-goals', [HealthGoalsController::class, 'store']);
    Route::delete('/health-goals/{id}', [HealthGoalsController::class, 'destroy']);

    // Health Metrics
    Route::get('/health-metrics', [HealthMetricsController::class, 'index']);
    Route::post('/health-metrics/water', [HealthMetricsController::class, 'addWater']);

    // Schedule Workout
    Route::get('/schedule-workout', [ScheduleWorkoutController::class, 'index']);
    Route::post('/schedule-workout', [ScheduleWorkoutController::class, 'store']);
    Route::get('/schedule-workout/exercises', [ScheduleWorkoutController::class, 'getExercises']);

    // Start Workout
    Route::get('/start-workout', [StartWorkoutController::class, 'index']);
    Route::post('/start-workout/{workout}/finish', [StartWorkoutController::class, 'finish']);
    Route::post('/start-workout/refresh-strava', [StartWorkoutController::class, 'refreshStrava']);

    // Strava
    //Route::post('/strava/disconnect', [StravaController::class, 'disconnect']);

    // Meal Tracking
    Route::get('/meals', [MealTrackingController::class, 'index']);
    Route::post('/meals', [MealTrackingController::class, 'store']);
    Route::get('/meals/next', [MealTrackingController::class, 'nextMeal']);

    // Food Items
    Route::get('/food-items', [FoodItemController::class, 'index']);
    Route::post('/food-items', [FoodItemController::class, 'store']);
    Route::get('/food-items/search', [FoodItemController::class, 'search']);
    Route::delete('/food-items/{foodItem}', [FoodItemController::class, 'destroy']);

    // Meal Types
    Route::get('/meal-types', [MealTypeController::class, 'index']);
});




// Login routes die doorsturen naar login-backend
//Routes voor users
Route::prefix('auth')->group(function () {

    Route::post('/login', function (Request $request) {
        Log::info('[/auth/login] Request: ' . json_encode($request->all()));
        $response = Http::post('http://nginxlogin/api/auth/login', $request->all());

        Log::info('[/auth/login] Response: ' . json_encode($response->json()));

        // Maak backend response
        $backendResponse = response()->json($response->json(), $response->status());

        // Stuur Set-Cookie headers direct door
        $setCookieHeaders = $response->headers()['Set-Cookie'] ?? [];

        foreach ($setCookieHeaders as $cookieHeader) {
            // Voeg elke cookie toe als separate header
            $backendResponse->headers->set('Set-Cookie', $cookieHeader, false);
        }

        return $backendResponse;
    });

    Route::post('/register', function (Request $request) {
        $response = Http::post('http://nginxlogin/api/auth/register', $request->all());
        return response()->json($response->json(), $response->status());
    });

    Route::get('/user', function (Request $request) {
        // 🔥 BELANGRIJK: Stuur cookies door naar login backend
        $cookies = $request->cookies->all();

        $response = Http::withHeaders([
            'Cookie' => implode('; ', array_map(
                fn($name, $value) => "$name=$value",
                array_keys($cookies),
                $cookies
            ))
        ])->get('http://nginxlogin/api/auth/user');

        return response()->json($response->json(), $response->status());
    });

    Route::post('/refresh', function (Request $request) {
        // Debug alle beschikbare informatie
        $allHeaders = $request->headers->all();
        $allCookies = $request->cookies->all();
        $cookieHeader = $request->header('Cookie');
        Log::info('🔁 Main backend: /auth/refresh called', [
            'request_data' => $request->all(),
            'headers' => $allHeaders,
            'cookies' => $allCookies

        ]);
        // Als er geen cookies zijn, probeer dan handmatig de Cookie header te construeren
        // if (empty($allCookies) && !$cookieHeader) {
        //     Log::warning('No cookies found in request, attempting to construct Cookie header manually');

        //     // Probeer cookies uit de headers te halen
        //     foreach ($allHeaders as $name => $values) {
        //         if (strtolower($name) === 'cookie') {
        //             $cookieHeader = is_array($values) ? implode('; ', $values) : $values;
        //             Log::info('Found Cookie header in headers: ' . $cookieHeader);
        //             break;
        //         }
        //     }
        // }

        try {
            // Gebruik withoutVerifying() voor eenvoud
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Cookie' => $cookieHeader ?: '',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('http://nginxlogin/api/auth/refresh');

            $backendResponse = response()->json($response->json(), $response->status());

            // Handle Set-Cookie headers
            if (isset($response->headers()['Set-Cookie'])) {
                foreach ($response->headers()['Set-Cookie'] as $cookie) {
                    $backendResponse->header('Set-Cookie', $cookie, false);
                }
            }

            return $backendResponse;

        } catch (\Exception $e) {
            Log::error('Refresh error: ' . $e->getMessage());
            return response()->json(['error' => 'Refresh failed: ' . $e->getMessage()], 500);
        }
    });

    Route::post('/logout', function (Request $request) {
        // Stuur cookies door naar login backend voor logout
        $cookies = $request->cookies->all();
        $cookieHeader = implode('; ', array_map(
            fn($name, $value) => "$name=$value",
            array_keys($cookies),
            $cookies
        ));

        $response = Http::withHeaders([
            'Cookie' => $cookieHeader
        ])->post('http://nginxlogin/api/auth/logout');

        // 🔥 BELANGRIJK: Verwijder de cookies uit de browser
        $backendResponse = response()->json($response->json(), $response->status());

        // Verwijder zowel token als refresh_token cookies
        $backendResponse->cookie(cookie()->forget('token'));
        $backendResponse->cookie(cookie()->forget('refresh_token'));

        // OF alternatief: verloopt in het verleden
        $backendResponse->cookie(cookie('token', '', -1));
        $backendResponse->cookie(cookie('refresh_token', '', -1));

        return $backendResponse;
    });

    Route::get('/me', function (Request $request) {
        $cookieHeader = $request->header('Cookie');

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Cookie' => $cookieHeader ?: '',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->get('http://nginxlogin/api/me');

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Me endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Authentication check failed'], 500);
        }
    });


    Route::get('/meInfo', function (Request $request) {
        $cookieHeader = $request->header('Cookie');

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Cookie' => $cookieHeader ?: '',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->get('http://nginxlogin/api/meInfo');

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Me endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Authentication check failed'], 500);
        }
    });

    Route::post('/verify-2fa', function (Request $request) {
        try {
            // Log::info('🔁 Main backend: /auth/verify-2fa called', ['request_data' => $request->all()]);

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Cookie' => $request->header('Cookie', ''),
                    'Authorization' => $request->header('Authorization', ''),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('http://nginxlogin/api/verify-2fa', $request->all());

            // Log::info('🔁 Login backend response status: ' . $response->status());

            // 🔥 DETAILED LOGGING VAN ALLE HEADERS
            $allHeaders = $response->headers();
            // Log::info('🔁 Login backend ALL headers:', $allHeaders);

            $setCookieHeaders = $allHeaders['Set-Cookie'] ?? [];
            // Log::info('🔁 Login backend Set-Cookie headers:', $setCookieHeaders);

            // Maak de response
            $backendResponse = response()->json($response->json(), $response->status());

            // 🔥 STUUR ALLE SET-COOKIE HEADERS DOOR
            if (!empty($setCookieHeaders)) {
                foreach ($setCookieHeaders as $cookie) {
                    // Log::info('🔁 Forwarding cookie: ' . $cookie);
                    $backendResponse->headers->set('Set-Cookie', $cookie, false);
                }
            } else {
                Log::warning('🔁 No Set-Cookie headers found in login backend response!');
            }

            // Log wat er uiteindelijk wordt teruggestuurd
            $finalHeaders = $backendResponse->headers->all();
            // Log::info('🔁 Final main backend headers:', [
            //     'set-cookie' => $finalHeaders['set-cookie'] ?? 'No cookies set'
            // ]);

            return $backendResponse;

        } catch (\Exception $e) {
            Log::error('Verify 2FA endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to verify 2FA'], 500);
        }
    });

    Route::post('/forgot-password', function (Request $request) {
        try {
            // Log::info('🔁 Main backend: /auth/forgot-password called', ['request_data' => $request->all()]);

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('http://nginxlogin/api/forgot-password', $request->all());

            // Log::info('🔁 Login backend response status: ' . $response->status());
            // Log::info('🔁 Login backend response: ' . json_encode($response->json()));

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Forgot password endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process request'], 500);
        }
    });
    Route::post('/reset-password', function (Request $request) {
        try {
            // Log::info('🔁 Main backend: /auth/forgot-password called', ['request_data' => $request->all()]);

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('http://nginxlogin/api/reset-password', $request->all());

            // Log::info('🔁 Login backend response status: ' . $response->status());
            // Log::info('🔁 Login backend response: ' . json_encode($response->json()));

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Forgot password endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process request'], 500);
        }
    });

    Route::post('/setup-authenticator-2fa', function (Request $request) {
        try {
            // Log::info('🔁 Main backend: setup-authenticator-2fa');

            // Probeer JWT token uit cookie te halen
            $token = $request->cookie('token');

            if (!$token) {
                Log::error('❌ No token cookie found');
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            // Log::info('✅ Found token cookie, length: ' . strlen($token));

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token, // 🔥 Stuur als Bearer token
                ])
                ->post('http://nginxlogin/api/setup-authenticator-2fa', $request->all());

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Setup authenticator endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process request'], 500);
        }
    });

    Route::post('/verify-authenticator-2fa', function (Request $request) {
        try {
            // Log::info('🔁 Main backend: verify-authenticator-2fa');

            // Probeer JWT token uit cookie te halen
            $token = $request->cookie('token');

            if (!$token) {
                Log::error('❌ No token cookie found');
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            // Log::info('✅ Found token cookie, length: ' . strlen($token));

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token, // 🔥 Stuur als Bearer token
                ])
                ->post('http://nginxlogin/api/verify-authenticator-2fa', $request->all());

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Verify 2FA endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to verify 2FA'], 500);
        }
    });

    Route::post('/disable-2fa', function (Request $request) {
        try {
            // Log::info('🔁 Main backend: disable-2fa', ['request_data' => $request->all()]);

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('http://nginxlogin/api/disable-2fa', $request->all());

            // Log::info('🔁 Login backend response status: ' . $response->status());
            // Log::info('🔁 Login backend response: ' . json_encode($response->json()));

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            Log::error('Forgot password endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process request'], 500);
        }
    });

    Route::post('/verify-recovery-code', function (Request $request) {
        try {
            // Log::info('🔁 Main backend: verify-recovery-code', ['request_data' => $request->all()]);

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('http://nginxlogin/api/verify-recovery-code', $request->all());

            // Log::info('🔁 Login backend response status: ' . $response->status());
            // Log::info('🔁 Login backend response: ' . json_encode($response->json()));

            // 🔥 BELANGRIJK: Stuur Set-Cookie headers door naar de frontend
            $backendResponse = response()->json($response->json(), $response->status());
            // Log::info('Response: ' . json_encode($response->json()));
            if (isset($response->headers()['Set-Cookie'])) {
                foreach ($response->headers()['Set-Cookie'] as $cookie) {
                    $backendResponse->header('Set-Cookie', $cookie, false);
                }
            }

            return $backendResponse;

        } catch (\Exception $e) {
            Log::error('Forgot password endpoint error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process request'], 500);
        }
    });
});