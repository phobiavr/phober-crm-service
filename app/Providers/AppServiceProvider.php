<?php

namespace App\Providers;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'crm-customer' => Customer::class,
        ]);
    }
}
