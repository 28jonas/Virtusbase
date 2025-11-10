<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ScheduleWorkoutController extends BaseApiController
{
    public function index()
    {
        $userId = request()->user['id'];
        $scheduleItems = Workout::where('profile_id', $userId)
            ->where('scheduled_at', '>=', now()->format('Y-m-d'))
            ->whereNull('completed_at')
            ->orderBy('scheduled_at')
            ->get();

        return response()->json($scheduleItems);
    }

    public function getExercises()
    {
        $exercises = Exercise::where('is_approved', true)
            ->orderBy('name')
            ->get();

        return response()->json($exercises);
    }

    public function store(Request $request)
    {
        $userId = request()->user['id'];
        Log::info('Scheduling workout for user ID: ' . $userId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'duration' => 'required|string|max:50',
            'schedule_at' => 'required|date',
            'notes' => 'nullable|string',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sets' => 'required|numeric|min:1',
            'exercises.*.reps' => 'required|numeric|min:1',
            'exercises.*.weight' => 'required|numeric|min:0',
        ]);

        // Create the workout
        $workout = Workout::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'duration' => $validated['duration'],
            'scheduled_at' => $validated['schedule_at'],
            'notes' => $validated['notes'],
            'profile_id' => $userId
        ]);

        // Add exercises
        foreach ($validated['exercises'] as $exercise) {
            $workout->exercises()->attach($exercise['exercise_id'], [
                'sets' => $exercise['sets'],
                'reps' => $exercise['reps'],
                'weight' => $exercise['weight'],
            ]);
        }

        $workout->load('exercises');

        return response()->json($workout, 201);
    }
}