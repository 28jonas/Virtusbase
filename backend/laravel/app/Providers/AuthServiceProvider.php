<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\ShoppingList;
use App\Models\Family;
use App\Policies\CalendarPolicy;
use App\Policies\EventPolicy;
use App\Policies\ShoppingListPolicy;
use App\Policies\FamilyPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Calendar::class => CalendarPolicy::class,
        Event::class => EventPolicy::class,
        ShoppingList::class => ShoppingListPolicy::class,
        Family::class => FamilyPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Optioneel: aanvullende Gates voor algemene permissies
        Gate::define('manage-family', [FamilyPolicy::class, 'manageMembers']);
        Gate::define('invite-members', [FamilyPolicy::class, 'inviteMembers']);
        Gate::define('remove-members', [FamilyPolicy::class, 'removeMembers']);
    }
}