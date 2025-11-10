<?php
// app/Models/MealType.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }
}