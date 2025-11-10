<?php

namespace App\Policies;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShoppingListPolicy
{
    public function view(User $user, ShoppingList $shoppingList): Response
    {
        // Eigenaar van de lijst mag altijd zien
        if ($shoppingList->owner->is($user)) {
            return Response::allow();
        }

        // Lijst is van een gezin waar user lid van is
        if ($shoppingList->owner_type === \App\Models\Family::class) {
            $family = $shoppingList->owner;
            if ($user->families->contains($family->id)) {
                return Response::allow();
            }
        }

        // Lijst is gedeeld met deze gebruiker
        if ($shoppingList->sharedWithUsers->contains($user->id)) {
            return Response::allow();
        }

        return Response::deny('You do not have access to this shopping list.');
    }

    public function create(User $user): Response
    {
        return Response::allow();
    }

    public function update(User $user, ShoppingList $shoppingList): Response
    {
        // Eigenaar mag lijst aanpassen
        if ($shoppingList->owner->is($user)) {
            return Response::allow();
        }

        // Voor gezinslijsten: ouders mogen ook aanpassen
        if ($shoppingList->owner_type === \App\Models\Family::class) {
            $family = $shoppingList->owner;
            if ($user->getRoleInFamily($family) === 'parent') {
                return Response::allow();
            }
        }

        // Gedeelde gebruiker met edit rechten mag aanpassen
        if ($shoppingList->sharedWithUsers->contains($user->id)) {
            $sharing = $shoppingList->sharedWithUsers->find($user->id)->pivot;
            if ($sharing->permission_level === 'edit') {
                return Response::allow();
            }
        }

        return Response::deny('You do not have permission to update this shopping list.');
    }

    public function delete(User $user, ShoppingList $shoppingList): Response
    {
        // Alleen eigenaar mag lijst verwijderen
        return $shoppingList->owner->is($user) 
            ? Response::allow() 
            : Response::deny('Only the owner can delete this shopping list.');
    }
}