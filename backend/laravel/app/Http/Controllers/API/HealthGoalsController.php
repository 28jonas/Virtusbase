<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\HealthGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthGoalsController extends BaseApiController
{
    public function index()
    {
        $userId = request()->user['id'];
        $goals = HealthGoal::where('profile_id', $userId)
            ->orderBy('date')
            ->get()
            ->map(function ($goal) {
                $goal->progress = $this->calculateProgress($goal);
                //$goal->icon_class = $this->getIconClass($goal->type);
                return $goal;
            });

        return response()->json($goals);
    }

    public function store(Request $request)
    {
        $userId = request()->user['id'];
        $validated = $request->validate([
            'title' => 'required|string',
            'target' => 'required|numeric|min:0',
            'current' => 'required|numeric|min:0',
            'goalDate' => 'required|date',
            'type' => 'required|string',
            'color' => 'nullable|string',
        ]);

        // Default colors based on type if not provided
        if (empty($validated['color'])) {
            $validated['color'] = match ($validated['type']) {
                'Weight' => '#3b82f6',
                'Calories' => '#f97316',
                'Steps' => '#10b981',
                'Workout' => '#8b5cf6',
                default => '#6366f1',
            };
        }

        $goal = HealthGoal::create([
            'title' => $validated['title'],
            'target' => $validated['target'],
            'current' => $validated['current'],
            'date' => $validated['goalDate'],
            'type' => $validated['type'],
            'color' => $validated['color'],
            'profile_id' => $userId,
        ]);

        $goal->progress = $this->calculateProgress($goal);
        $goal->icon_class = $this->getIconClass($goal->type);

        return response()->json($goal, 201);
    }

    public function destroy($id)
    {
        $userId = request()->user['id'];
        $goal = HealthGoal::where('profile_id', $userId->findOrFail($id));
        $goal->delete();

        return response()->json(['message' => 'Goal deleted successfully']);
    }

    private function calculateProgress($goal)
    {
        $percentage = ($goal->current / $goal->target) * 100;
        return min(round($percentage), 100);
    }

    private function getIconClass($type)
    {
        return match ($type) {
            'Weight' => 'fa-weight-scale',
            'Calories' => 'fa-fire',
            'Steps' => 'fa-person-walking',
            'Workout' => 'fa-dumbbell',
            default => 'fa-bullseye',
        };
    }
}