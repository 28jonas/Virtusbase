<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\Family;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Container\Attributes\Log;

class CalendarPolicy
{
    /**
     * Check if user can view a calendar
     */
    public function view(User $user, Calendar $calendar): Response
    {
        Log::info("Checking view permission for user ID: {$user->id} on calendar ID: {$calendar->id}");
        // Als de eigenaar een profiel is → check user_id
        if ($calendar->owner_type === Profile::class) {
            if ($calendar->owner && $calendar->owner->user_id === $user->id) {
                return Response::allow();
            }
        }

        // Als eigenaar een gezin is → check of user lid is
        if ($calendar->owner_type === Family::class) {
            $family = $calendar->owner;
            if ($family && $user->families->contains($family->id)) {
                return Response::allow();
            }
        }

        // Publieke kalenders zijn voor iedereen zichtbaar
        if ($calendar->is_public) {
            return Response::allow();
        }

        return Response::deny('You do not have access to this calendar.');
    }

    /**
     * Check if user can create a calendar
     */
    public function create(User $user): Response
    {
        Log::info("Checking create permission for user ID: {$user->id}");
        // Iedere ingelogde gebruiker mag een kalender aanmaken
        return Response::allow();
    }

    /**
     * Check if user can update a calendar
     */
    public function update(User $user, Calendar $calendar): Response
    {
        // Profiel-kalenders → alleen eigenaar (via Profile → user_id)
        if ($calendar->owner_type === Profile::class) {
            if ($calendar->owner && $calendar->owner->user_id === $user->id) {
                return Response::allow();
            }
        }

        // Familie-kalenders → ouders mogen aanpassen
        if ($calendar->owner_type === Family::class) {
            $family = $calendar->owner;
            if ($family && $user->getRoleInFamily($family) === 'parent') {
                return Response::allow();
            }
        }

        return Response::deny('Only the owner can update this calendar.');
    }

    /**
     * Check if user can delete a calendar
     */
    public function delete(User $user, Calendar $calendar): Response
    {
        // Profiel-kalenders → alleen eigenaar
        if ($calendar->owner_type === Profile::class) {
            if ($calendar->owner && $calendar->owner->user_id === $user->id) {
                return Response::allow();
            }
        }

        // Familie-kalenders → alleen ouders mogen verwijderen
        if ($calendar->owner_type === Family::class) {
            $family = $calendar->owner;
            if ($family && $user->getRoleInFamily($family) === 'parent') {
                return Response::allow();
            }
        }

        return Response::deny('Only the owner can delete this calendar.');
    }
}