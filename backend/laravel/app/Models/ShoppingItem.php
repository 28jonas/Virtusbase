<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingItem extends Model
{
    protected $fillable = ['shopping_list_id', 'name', 'quantity', 'is_completed', 'added_by_profile_id', 'completed_at'];

    public function list() {
        return $this->belongsTo(ShoppingList::class, 'shopping_list_id');
    }

    public function addedBy() {
        return $this->belongsTo(Profile::class, 'added_by_profile_id');
    }
}