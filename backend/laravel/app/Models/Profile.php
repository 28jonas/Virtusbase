<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [ 'id', 'first_name', 'last_name', 'email', 'profile_picture', 'date_of_birth'];

    protected $table = 'profiles';
    // Optioneel: als je geen auto-increment wilt
    public $incrementing = false;
    protected $keyType = 'int';
    
    // RELATIES
    public function families() {
        return $this->belongsToMany(Family::class, 'family_profile', 'profile_id', 'family_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function todo() {
        return $this->hasMany(Todo::class);
    }

    public function waterintake(){
        return $this->hasMany(WaterIntake::class);
    }

    // Polymorfe relaties
    public function ownedCalendars() {
        return $this->morphMany(Calendar::class, 'owner');
    }

    public function ownedEvents() {
        return $this->morphMany(Event::class, 'owner');
    }

    public function ownedShoppingLists() {
        return $this->morphMany(ShoppingList::class, 'owner');
    }

    // Helper methods
    public function getRoleInFamily(Family $family): ?string {
        return $this->families()->where('family_id', $family->id)->first()?->pivot->role;
    }

    public function hasPermissionInFamily(Family $family, string $permission): bool {
        $role = $this->getRoleInFamily($family);
        return \App\Enums\FamilyRolePermission::can($role, $permission);
    }
}