<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
//
    use SoftDeletes;
    protected $fillable = ['name', 'type', 'color'];

    public function scopeShopDepartments($query)
    {
        return $query->where('type', 'shop_department');
    }

    public function scopehabitCategories($query)
    {
        return $query->where('type', 'habit_category');
    }

    public function scopeEventCategories($query)
    {

        return $query->where('type', 'event_category');
    }

    public function scopefinancialgoalCategories($query)
    {

        return $query->where('type', 'financialgoal_category');

        /*
         return cache()->remember("financial_category_{$id}", 3600, function() use ($id) {
        return static::financialgoalCategories()
               ->where('id', $id)
               ->first()
               ->name;
    });
         * */
    }

    // public function groceryItems()
    // {
    //     return $this->morphedByMany(GroceryItem::class, 'categoryable');
    // }

    // public function expenses()
    // {
    //     return $this->morphedByMany(Expense::class, 'categoryable');
    // }

    // public function incomes()
    // {
    //     return $this->morphedByMany(Income::class, 'categoryable');
    // }

    public function habits()
    {
        return $this->morphedByMany(Habit::class, 'categoryable');
    }

    public function events()
    {
        return $this->morphedByMany(Event::class, 'categoryable');
    }

    // public function goals()  // Optioneel, als FinancialGoal ook categoriseerbaar is
    // {
    //     return $this->morphedByMany(Goal::class, 'categoryable');
    // }
}