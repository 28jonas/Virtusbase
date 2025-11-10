<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo', 'example', 'format'];

    public function cards()
    {
        return $this->hasMany(Bankingcard::class);
    }
}