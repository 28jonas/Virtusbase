<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\Workout;
use App\Services\StravaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StartWorkoutController extends BaseApiController
{
    protected $stravaService;

    // public function __construct(StravaService $stravaService)
    // {
    //     $this->stravaService = $stravaService;
    // }

    public function index()
    {
        $userId = request()->user['id'];
        $user = request()->user;
        // Get next scheduled workout
        $scheduledWorkout = Workout::where('profile_id', $userId)
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->whereNull('completed_at')
            ->orderBy('scheduled_at')
            ->first();

        // Get most recent completed workout
        $previousWorkout = Workout::where('profile_id', $userId)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->first();

        $latestActivity = null;
        $activitySource = null;
        $stravaError = null;

        // Check if user has Strava connected
        // if ($user->strava_access_token) {
        //     try {
        //         $stravaActivity = $this->stravaService->getLatestActivity($user);

        //         if ($stravaActivity) {
        //             $latestWorkout = Workout::latest('completed_at')->first();
        //             $activityDate = Carbon::parse($stravaActivity['start_date_local']);

        //             if ($latestWorkout && $latestWorkout->completed_at > $activityDate) {
        //                 $latestActivity = Workout::find($latestWorkout->id);
        //                 $activitySource = 'database';
        //             } else {
        //                 $latestActivity = $this->formatStravaActivity($stravaActivity);
        //                 $activitySource = 'strava';
        //             }
        //         }
        //     } catch (\Exception $e) {
        //         $stravaError = $e->getMessage();
        //         if (str_contains($e->getMessage(), 'token') || str_contains($e->getMessage(), 'autorisatie')) {
        //             $stravaError = 'Strava verbinding verlopen - koppel opnieuw';
        //         }
        //     }
        // }

        // Fallback to database activity
        if (!$latestActivity && $previousWorkout) {
            $latestActivity = [
                'name' => $previousWorkout->name ?? 'Workout',
                'distance' => $previousWorkout->distance ?? 0,
                'moving_time' => $previousWorkout->duration ?? 0,
                'type' => $previousWorkout->type ?? 'Workout',
                'start_date' => $previousWorkout->completed_at ?? $previousWorkout->created_at,
                'elevation_gain' => 0,
                'average_speed' => 0,
            ];
            $activitySource = 'database';
        }

        return response()->json([
            'scheduledWorkout' => $scheduledWorkout,
            'previousWorkout' => $previousWorkout,
            'latestActivity' => $latestActivity,
            'activitySource' => $activitySource,
            'stravaError' => $stravaError,
        ]);
    }

    public function finish(Request $request, Workout $workout)
    {
        $userId = request()->user['id'];
        Log::info('User ID: ' . $userId);
        Log::info('Workout Profile ID: ' . $workout->profile_id);
        if ($workout->profile_id != $userId) {
            Log::info("response unauthorized" );
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        Log::info("check oke");
        $workout->completed_at = Carbon::now();
        $workout->save();

        Cache::forget('strava_activity_' . $userId);

        return response()->json(['message' => 'Workout voltooid!']);
    }

    public function refreshStrava(Request $request)
    {
        $user = Auth::user();
        Cache::forget('strava_activity_' . $user->id);

        // Reload data
        return $this->index();
    }

    protected function formatStravaActivity($activity)
    {
        return [
            'name' => $activity['name'] ?? 'Onbekende activiteit',
            'distance' => $activity['distance'] ?? 0,
            'moving_time' => $activity['moving_time'] ?? 0,
            'type' => $activity['type'] ?? 'Workout',
            'start_date' => isset($activity['start_date'])
                ? Carbon::parse($activity['start_date'])
                : now(),
            'elevation_gain' => $activity['total_elevation_gain'] ?? 0,
            'average_speed' => $activity['average_speed'] ?? 0,
        ];
    }
}