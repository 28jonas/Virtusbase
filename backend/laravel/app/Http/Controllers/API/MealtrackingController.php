<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\MealType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealTrackingController extends BaseApiController
{
    public function index(Request $request)
    {
        $userId = request()->user['id'];
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $meals = Meal::with(['mealType', 'foodItems'])
            ->where('profile_id', $userId)
            ->where('date', $date)
            ->orderBy('time')
            ->get()
            ->map(function ($meal) {
                $meal->total_nutrition = $this->calculateMealNutrition($meal);
                return $meal;
            });

        $mealTypes = MealType::all();

        return response()->json([
            'meals' => $meals,
            'mealTypes' => $mealTypes,
            'date' => $date
        ]);
    }

    public function store(Request $request)
    {
        $userId = request()->user['id'];
        $validated = $request->validate([
            'meal.meal_type_id' => 'required|exists:meal_types,id',
            'meal.name' => 'required|string|max:255',
            'meal.notes' => 'nullable|string',
            'meal.time' => 'required|date_format:H:i',
            'meal.date' => 'required|date',
            'food_items' => 'required|array|min:1',
            'food_items.*.id' => 'required|exists:food_items,id',
            'food_items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $meal = Meal::create([
            'profile_id' => $userId,
            'meal_type_id' => $validated['meal']['meal_type_id'],
            'name' => $validated['meal']['name'],
            'notes' => $validated['meal']['notes'] ?? null,
            'time' => $validated['meal']['time'],
            'date' => $validated['meal']['date'],
        ]);

        foreach ($validated['food_items'] as $foodItem) {
            $meal->foodItems()->attach($foodItem['id'], [
                'quantity' => $foodItem['quantity'],
            ]);
        }

        $meal->load(['mealType', 'foodItems']);
        $meal->total_nutrition = $this->calculateMealNutrition($meal);

        return response()->json([
            'message' => 'Meal created successfully',
            'meal' => $meal
        ]);
    }

    public function nextMeal()
    {
        $userId = request()->user['id'];
        $now = now()->format('H:i:s');

        // Find future meals today
        $nextMeal = Meal::with(['mealType', 'foodItems'])
            ->where('profile_id', $userId)
            ->whereDate('date', Carbon::today())
            ->whereTime('time', '>', $now)
            ->orderBy('time')
            ->first();

        // If no future meals today, find meals tomorrow
        if (!$nextMeal) {
            $nextMeal = Meal::with(['mealType', 'foodItems'])
                ->where('profile_id', $userId)
                ->whereDate('date', Carbon::tomorrow())
                ->orderBy('time')
                ->first();
        }

        if ($nextMeal) {
            return response()->json([
                'meal' => $nextMeal,
                'mealType' => $nextMeal->mealType->name ?? 'Meal',
                'mealTime' => Carbon::parse($nextMeal->time)->format('H:i')
            ]);
        }

        return response()->json([
            'meal' => null,
            'mealType' => '',
            'mealTime' => ''
        ]);
    }

    private function calculateMealNutrition(Meal $meal)
    {
        $totals = [
            'calories' => 0,
            'protein' => 0,
            'fat' => 0,
            'carbs' => 0,
            'fiber' => 0,
            'sugar' => 0,
            'sodium' => 0,
        ];

        foreach ($meal->foodItems as $foodItem) {
            $quantity = $foodItem->pivot->quantity;
            $totals['calories'] += $foodItem->calories * $quantity;
            $totals['protein'] += $foodItem->protein * $quantity;
            $totals['fat'] += $foodItem->fat * $quantity;
            $totals['carbs'] += $foodItem->carbs * $quantity;
            $totals['fiber'] += $foodItem->fiber * $quantity;
            $totals['sugar'] += $foodItem->sugar * $quantity;
            $totals['sodium'] += $foodItem->sodium * $quantity;
        }

        return $totals;
    }
}