<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'profile_id',
        'title',
        'date',
        'type',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'exercise_workouts')
            ->withPivot(['sets', 'reps', 'weight', 'notes', 'duration'])
            ->withTimestamps();
    }

    public function isCompleted()
    {
        return $this->completed_at !== null;
    }


}