<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthGoal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'target',
        'current',
        'date',
        'type',
        'color',
        'profile_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'target' => 'float',
        'current' => 'float',
        'date' => 'date',
    ];

    /**
     * Get the user that owns the fitness goal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}