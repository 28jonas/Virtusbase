<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['title', 'description', 'date', 'completed', 'completed_at', 'profile_id', 'notify_at'];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
