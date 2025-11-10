<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Family;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class EventPolicy
{
    /**
     * Check if user can view an event
     */
    public function view(User $user, Event $event): Response
    {
        Log::info("Checking view permission for user ID: {$user->id} on event ID: {$event->id}");

        // Event van een profiel → check of dit profiel aan user hangt
        if ($event->owner_type === Profile::class) {
            if ($event->owner && $event->owner->user_id === $user->id) {
                return Response::allow();
            }
        }

        // Event van een gezin → check lidmaatschap
        if ($event->owner_type === Family::class) {
            $family = $event->owner;
            if ($family && $user->families->contains($family->id)) {
                return Response::allow();
            }
        }

        // Als user de kalender mag zien
        if ($user->can('view', $event->calendar)) {
            return Response::allow();
        }

        return Response::deny('You do not have access to this event.');
    }

    /**
     * Check if user can create an event
     */
    public function create(User $user): Response
    {
        Log::info("Checking create permission for user ID: {$user->id}");
        // Iedereen die ingelogd is mag een event maken
        return Response::allow();
    }

    /**
     * Check if user can update an event
     */
    public function update(User $user, Event $event): Response
    {
        Log::info("Checking update permission for user ID: {$user->id} on event ID: {$event->id}");

        // Event van profiel → alleen eigenaar
        if ($event->owner_type === Profile::class) {
            if ($event->owner && $event->owner->user_id === $user->id) {
                return Response::allow();
            }
        }

        // Event van gezin → ouders mogen aanpassen
        if ($event->owner_type === Family::class) {
            $family = $event->owner;
            if ($family && $user->getRoleInFamily($family) === 'parent') {
                return Response::allow();
            }
        }

        // Als gebruiker de kalender mag aanpassen
        if ($user->can('update', $event->calendar)) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to update this event.');
    }

    /**
     * Check if user can delete an event
     */
    public function delete(User $user, Event $event): Response
    {
        return $this->update($user, $event);
    }
}