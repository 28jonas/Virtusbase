<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory;

    protected $table = 'incomes';

    protected $fillable = ['category_id', 'amount', 'description', 'date', 'bankingcard_id'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }


    public function card()
    {
        return $this->belongsTo(Card::class, 'bankingcard_id');
    }

    /*public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }*/

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('card', function($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }
}