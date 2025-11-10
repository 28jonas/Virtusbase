<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $table = 'bankingcards';

    protected $fillable = [
        'profile_id',
        'bank_id', 
        'card_number',
        'expiry_date',
        'balance',
        'last_four'
    ];

    // Relaties
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'bankingcard_id');
    }

    public function goals()
    {
        return $this->hasMany(Goal::class, 'bankingcard_id');
    }

    public function incomes()
    {
        return $this->hasMany(Income::class, 'bankingcard_id');
    }

    public function dailyBalanceUpdates()
    {
        return $this->hasMany(DailyBalanceUpdate::class, 'bankingcard_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'profile_id');
    }
}