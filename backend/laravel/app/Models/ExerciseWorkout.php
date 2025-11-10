<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseWorkout extends Model
{
    protected $fillable = [
        'workout_id',
        'exercise_id',
        'sets',
        'reps',
        'weight',
        'completed_at',
        'duration',
        'calories_burned',
        'notes',
    ];
}