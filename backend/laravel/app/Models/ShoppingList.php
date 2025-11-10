<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    protected $fillable = ['name', 'owner_id', 'owner_type', 'is_shared'];

    public function owner()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(ShoppingItem::class);
    }

    public function sharedWithUsers()
    {
        return $this->belongsToMany(Profile::class, 'shopping_list_sharings')
            ->withPivot('permission_level')
            ->withTimestamps();
    }

    public function addedBy()
    {
        return $this->belongsTo(Profile::class, 'added_by_profile_id');
    }

    public function familyOwner()
    {
        return $this->belongsTo(Family::class, 'owner_id');
    }

}