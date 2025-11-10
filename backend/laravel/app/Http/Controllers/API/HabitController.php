<?php

namespace App\Http\Controllers\API;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HabitController extends BaseApiController
{
    public function index(Request $request)
    {
        Log::info('Habit index request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'week_start' => 'nullable|date',
            'week_end' => 'nullable|date|after_or_equal:week_start',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $userId = request()->user['id'];

        // Bepaal de week range
        $currentDate = $request->has('week_start') 
            ? Carbon::parse($request->week_start)
            : Carbon::now();

        $weekDays = $this->generateWeekDays($currentDate);

        // Haal habits op voor de gebruiker
        $habits = Habit::where('profile_id', $userId)->get();

        // Transform habits met completion status voor elke dag
        $transformedHabits = $habits->map(function($habit) use ($weekDays) {
            $completionStatus = [];

            foreach ($weekDays as $day) {
                $completionStatus[$day['date']] = HabitLog::where('habit_id', $habit->id)
                    ->whereDate('completed_date', $day['date'])
                    ->exists();
            }

            return [
                'id' => $habit->id,
                'name' => $habit->name,
                'description' => $habit->description,
                'frequency' => $habit->frequency,
                'category' => $habit->category,
                'completionStatus' => $completionStatus,
                'current_streak' => $habit->current_streak,
                'best_streak' => $habit->best_streak,
                'created_at' => $habit->created_at,
                'updated_at' => $habit->updated_at,
            ];
        });

        $response = [
            'habits' => $transformedHabits,
            'week_days' => $weekDays,
            'current_date' => $currentDate->format('Y-m-d'),
            'week_range' => [
                'start' => $weekDays[0]['date'],
                'end' => $weekDays[6]['date']
            ]
        ];

        return $this->sendResponse($response, 'Habits retrieved successfully.');
    }

    public function store(Request $request)
    {
        Log::info('Store habit request data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'category' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $userId = request()->user['id'];
        Log::info('User ID:', [$userId]);

        $habit = Habit::create([
            'name' => $request->name,
            'description' => $request->description,
            'frequency' => $request->frequency,
            'category' => $request->category,
            'current_streak' => 0,
            'best_streak' => 0,
            'profile_id' => $userId,
        ]);

        // Category relatie (als je categories gebruikt)
        if ($request->category_id) {
            $habit->category()->sync([$request->category_id]);
        }

        return $this->sendResponse($habit, 'Habit created successfully.', 201);
    }

    public function show(Habit $habit)
    {
        // Authorisatie: controleer of habit van de gebruiker is
        $userId = request()->user['id'];
        if ($habit->user_id != $userId) {
            return $this->sendError('Unauthorized access to habit.', [], 403);
        }

        $habit->load('logs');

        return $this->sendResponse($habit, 'Habit retrieved successfully.');
    }

    public function update(Request $request, Habit $habit)
    {
        Log::info('Update habit request data:', $request->all());
        
        // Authorisatie: controleer of habit van de gebruiker is
        $userId = request()->user['id'];
        Log::info('User ID:', [$userId]);
        Log::info('Habit User ID:', [$habit->profile_id]);
        if ($habit->profile_id != $userId) {
            return $this->sendError('Unauthorized access to habit.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'category' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $habit->update([
            'name' => $request->name,
            'description' => $request->description,
            'frequency' => $request->frequency,
            'category' => $request->category,
        ]);

        // Update category relatie
        if ($request->has('category_id')) {
            $habit->category()->sync([$request->category_id]);
        }

        return $this->sendResponse($habit, 'Habit updated successfully.');
    }

    public function destroy(Habit $habit)
    {
        // Authorisatie: controleer of habit van de gebruiker is
        $userId = request()->user['id'];
        if ($habit->profile_id != $userId) {
            return $this->sendError('Unauthorized access to habit.', [], 403);
        }

        $habit->delete();

        return $this->sendResponse(null, 'Habit deleted successfully.');
    }

    public function toggleCompletion(Habit $habit, Request $request)
    {
        Log::info('Toggle completion request:', $request->all());
        
        // Authorisatie: controleer of habit van de gebruiker is
        $userId = request()->user['id'];
        if ($habit->profile_id != $userId) {
            return $this->sendError('Unauthorized access to habit.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $date = $request->date;

        // Check if habit is already completed for this date
        $isCompleted = $habit->getCompletionStatusForDate($date);

        if ($isCompleted) {
            // Delete completion log
            $habit->logs()->whereDate('completed_date', $date)->delete();
            $action = 'removed';
        } else {
            // Create new completion log
            $habit->logCompletionForDate($date);
            $action = 'added';
        }

        // Recalculate and update streak information
        $habit->updateStreak();
        $habit->refresh();

        return $this->sendResponse([
            'habit' => $habit,
            'action' => $action,
            'completed' => !$isCompleted,
            'date' => $date
        ], 'Habit completion status updated successfully.');
    }

    public function logCompletion(Habit $habit, Request $request)
    {
        // Authorisatie: controleer of habit van de gebruiker is
        $userId = request()->user['id'];
        if ($habit->profile_id != $userId) {
            return $this->sendError('Unauthorized access to habit.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        // Log completion with notes
        $habit->logCompletionForDate($request->date, $request->notes);
        $habit->updateStreak();
        $habit->refresh();

        return $this->sendResponse($habit, 'Habit completion logged successfully.');
    }

    public function getWeekData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $currentDate = Carbon::parse($request->date);
        $weekDays = $this->generateWeekDays($currentDate);

        return $this->sendResponse([
            'week_days' => $weekDays,
            'current_date' => $currentDate->format('Y-m-d'),
            'week_range' => [
                'start' => $weekDays[0]['date'],
                'end' => $weekDays[6]['date']
            ]
        ], 'Week data retrieved successfully.');
    }

    public function getStats()
    {
        $userId = request()->user['id'];

        $totalHabits = Habit::where('profile_id', $userId)->count();
        $completedToday = HabitLog::whereHas('habit', function($query) use ($userId) {
            $query->where('profile_id', $userId);
        })->whereDate('completed_date', Carbon::today())->count();

        $totalCompletions = HabitLog::whereHas('habit', function($query) use ($userId) {
            $query->where('profile_id', $userId);
        })->count();

        $longestStreak = Habit::where('profile_id', $userId)->max('best_streak');

        $stats = [
            'total_habits' => $totalHabits,
            'completed_today' => $completedToday,
            'total_completions' => $totalCompletions,
            'longest_streak' => $longestStreak,
        ];

        return $this->sendResponse($stats, 'Habit stats retrieved successfully.');
    }

    /**
     * Helper method om week dagen te genereren
     */
    private function generateWeekDays(Carbon $currentDate)
    {
        $weekDays = [];
        $startOfWeek = $currentDate->copy()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);

            $weekDays[] = [
                'date' => $day->format('Y-m-d'),
                'day' => $day->format('D'),
                'dayNumber' => $day->format('d'),
                'isToday' => $day->isToday(),
            ];
        }

        return $weekDays;
    }
}