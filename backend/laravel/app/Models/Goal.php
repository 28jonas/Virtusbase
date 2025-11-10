<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use HasFactory;

    protected $table = 'goals';

    protected $fillable = [
        'amount',
        'date',
        'bankingcard_id',
        'transfer',
    ];

    /**
     * Get the card associated with the goal.
     */
    public function card()
    {
        return $this->belongsTo(Card::class, 'bankingcard_id');
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }
}
