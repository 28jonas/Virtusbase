<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'meal_type_id',
        'name',
        'notes',
        'time',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mealType(): BelongsTo
    {
        return $this->belongsTo(MealType::class);
    }

    public function foodItems(): BelongsToMany
    {
        return $this->belongsToMany(FoodItem::class, 'meal_food_item')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // Bereken totale voedingswaarden van de maaltijd
    public function getTotalNutritionAttribute(): array
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

        foreach ($this->foodItems as $foodItem) {
            $multiplier = $foodItem->pivot->quantity;
            $totals['calories'] += $foodItem->calories * $multiplier;
            $totals['protein'] += $foodItem->protein * $multiplier;
            $totals['fat'] += $foodItem->fat * $multiplier;
            $totals['carbs'] += $foodItem->carbs * $multiplier;
            $totals['fiber'] += ($foodItem->fiber ?? 0) * $multiplier;
            $totals['sugar'] += ($foodItem->sugar ?? 0) * $multiplier;
            $totals['sodium'] += ($foodItem->sodium ?? 0) * $multiplier;
        }

        return $totals;
    }
}