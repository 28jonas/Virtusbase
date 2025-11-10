<?php
// app/Models/Habit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'frequency',
        'category',
        'current_streak',
        'best_streak',
        'profile_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function category()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function logCompletionForDate($date)
    {

        // Avoid duplicate entries for the same date
        $existingLog = $this->logs()->whereDate('completed_date', $date)->first();

        if (!$existingLog) {
            $log = new HabitLog([
                'completed_date' => $date,
            ]);

            $this->logs()->save($log);
            $this->updateStreak();
            $this->save();
            return $log;
        }

        return $existingLog;
    }

    /*public function updateStreak()
    {
        // Calculate current streak
        $streak = 0;
        $today = now()->startOfDay();
        $daysToCheck = $today->copy()->subDays(30); // Check last 30 days

        $logs = $this->logs()
            ->where('completed_date', '>=', $daysToCheck)
            ->orderBy('completed_date', 'desc')
            ->get();

        $lastDate = $today;

        foreach ($logs as $log) {
            $logDate = \Carbon\Carbon::parse($log->completed_date)->startOfDay();

            $monthDiff = $lastDate->diffInMonths($logDate);
            if ($monthDiff <= 1) {
                $streak++;
                $lastDate = $logDate;
            } else {
                break;
            }

            // For daily habits, check consecutive days
            if ($this->frequency === 'daily') {
                $dayDiff = $lastDate->diffInDays($logDate);
                if ($dayDiff <= 1) {
                    $streak++;
                    $lastDate = $logDate;
                } else {
                    break;
                }
            }
            // For weekly habits, check consecutive weeks
            else if ($this->frequency === 'weekly') {
                $weekDiff = $lastDate->diffInWeeks($logDate);
                if ($weekDiff <= 1) {
                    $streak++;
                    $lastDate = $logDate;
                } else {
                    break;
                }
            }
            // For monthly habits, check consecutive months
            else if ($this->frequency === 'monthly') {
                $monthDiff = $lastDate->diffInMonths($logDate);
                if ($monthDiff <= 1) {
                    $streak++;
                    $lastDate = $logDate;
                } else {
                    break;
                }
            }
        }

        $this->current_streak = $streak;

        if ($streak > $this->best_streak) {
            $this->best_streak = $streak;
        }

        $this->save();
    }*/

    public function updateStreak()
    {
        $streak = $this->logs()->count();
        $this->current_streak = $streak;
        $this->best_streak = max($streak, $this->best_streak);
        $this->save();
    }

    public function getCompletionStatusForDate($date)
    {
        return $this->logs()->whereDate('completed_date', $date)->exists();
    }
}
