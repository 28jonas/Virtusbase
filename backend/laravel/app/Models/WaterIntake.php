<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterIntake extends Model
{
    use HasFactory;

    protected $fillable = ['profile_id', 'amount', 'created_at'];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}