<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FoodItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'name',
        'image_path',
        'serving_size',
        'serving_unit',
        'calories',
        'protein',
        'fat',
        'carbs',
        'fiber',
        'sugar',
        'sodium',
        'is_public',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_food_item')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}