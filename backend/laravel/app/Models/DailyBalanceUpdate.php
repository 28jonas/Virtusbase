<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBalanceUpdate extends Model
{
    protected $fillable = [
        'snapshot_date',
        'balance',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'balance' => 'float'
    ];

    public function card()
    {
        return $this->belongsTo(Card::class, 'bankingcard_id');
    }
}