<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\WaterIntake;
use App\Services\SamsungHealthApiService;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthMetricsController extends BaseApiController
{
    public function index()
    {
        $userId = request()->user['id'];

        // Load water intake for today
        $waterIntake = WaterIntake::where('profile_id', $userId)
            ->whereDate('created_at', today())
            ->sum('amount');

        // Load calorie intake for today
        $caloriesConsumed = Meal::where('profile_id', $userId)
            ->where('date', today())
            ->where('time', '<=', now()->format('H:i:s'))
            ->with('foodItems')
            ->get()
            ->sum(function ($meal) {
                return $meal->total_nutrition['calories'] ?? 0;
            });

        $steps = 2500;
        $heartRate = 110;

        // If Samsung Health API integration is implemented
        // try {
        //     $samsungHealthService = app(SamsungHealthApiService::class);
        //     $healthData = $samsungHealthService->getTodayData($user->id);

        //     if (isset($healthData['steps'])) {
        //         $steps = $healthData['steps'];
        //     }

        //     if (isset($healthData['heart_rate'])) {
        //         $heartRate = $healthData['heart_rate'];
        //     }
        // } catch (\Exception $e) {
        //     // Use fallback data
        // }

        return response()->json([
            'steps' => $steps,
            'stepsGoal' => 5000,
            'waterIntake' => $waterIntake,
            'waterGoal' => 2000,
            'caloriesConsumed' => $caloriesConsumed,
            'caloriesGoal' => 2000,
            'heartRate' => $heartRate,
            'stepsPercentage' => min(100, ($steps / 5000) * 100),
            'waterPercentage' => min(100, ($waterIntake / 2000) * 100),
            'caloriesPercentage' => min(100, ($caloriesConsumed / 2000) * 100),
        ]);
    }

    public function addWater(Request $request)
    {
        $userId = request()->user['id'];

        $glassSize = 250; // ml

        WaterIntake::create([
            'profile_id' => $userId,
            'amount' => $glassSize
        ]);

        $newWaterIntake = WaterIntake::where('profile_id', $userId)
            ->whereDate('created_at', today())
            ->sum('amount');

        return response()->json([
            'waterIntake' => $newWaterIntake,
            'waterPercentage' => min(100, ($newWaterIntake / 2000) * 100),
        ]);
    }
}