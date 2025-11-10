<?php

namespace App\Policies;

use App\Models\Family;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FamilyPolicy
{
    public function view(User $user, Family $family): Response
    {
        // Alleen leden van het gezin mogen het zien
        return $user->families->contains($family->id)
            ? Response::allow()
            : Response::deny('You are not a member of this family.');
    }

    public function create(User $user): Response
    {
        // Iedere gebruiker mag een gezin aanmaken
        return Response::allow();
    }

    public function update(User $user, Family $family): Response
    {
        // Alleen eigenaars mogen het gezin aanpassen
        return $user->getRoleInFamily($family) === 'owner'
            ? Response::allow()
            : Response::deny('Only family owners can update the family.');
    }

    public function delete(User $user, Family $family): Response
    {
        // Alleen eigenaars mogen het gezin verwijderen
        return $user->getRoleInFamily($family) === 'owner'
            ? Response::allow()
            : Response::deny('Only family owners can delete the family.');
    }

    public function manageMembers(User $user, Family $family): Response
    {
        // Eigenaars en ouders mogen leden beheren
        $role = $user->getRoleInFamily($family);
        return in_array($role, ['owner', 'parent'])
            ? Response::allow()
            : Response::deny('You do not have permission to manage family members.');
    }

    public function inviteMembers(User $user, Family $family): Response
    {
        return $user->hasPermissionInFamily($family, 'invite_members')
            ? Response::allow()
            : Response::deny('You do not have permission to invite members.');
    }

    public function removeMembers(User $user, Family $family): Response
    {
        return $user->hasPermissionInFamily($family, 'remove_members')
            ? Response::allow()
            : Response::deny('You do not have permission to remove members.');
    }
}