<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'description', 'start', 'end', 'location', 'calendar_id'];
    protected $dates = ['start', 'end'];

    public function owner() {
        return $this->morphTo();
    }

    public function calendar() {
        return $this->belongsTo(Calendar::class);
    }
}