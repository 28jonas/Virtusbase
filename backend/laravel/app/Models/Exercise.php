<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'muscle_group',
        'equipment',
        'is_approved',
        'submitted_by',
    ];

    public function workout()
    {
        return $this->belongsToMany(Workout::class)
            ->withPivot(['sets', 'reps', 'weight', 'notes', 'schedule_at', 'duration']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}