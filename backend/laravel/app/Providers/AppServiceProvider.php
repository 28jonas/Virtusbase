<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user' => 'App\Models\Profile', // Map 'user' naar Profile model
            'family' => 'App\Models\Family',
            'income' => 'App\Models\Income',
            'expense' => 'App\Models\Expense',
            'goal' => 'App\Models\Goal',
        ]);
    }
}
