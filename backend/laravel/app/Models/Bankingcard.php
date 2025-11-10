<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Bankingcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_number',
        'expiry_date',
        'balance',
        'profile_id',
        'bank_id',
        'last_four',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    // public function expenses()
    // {
    //     return $this->hasMany(Expense::class);
    // }

    // public function incomes()
    // {
    //     return $this->hasMany(Income::class);
    // }

    // public function goals()
    // {
    //     return $this->hasMany(Goal::class);
    // }

    // public function dailyBalanceUpdates()
    // {
    //     return $this->hasMany(DailyBalanceUpdate::class);
    // }
}