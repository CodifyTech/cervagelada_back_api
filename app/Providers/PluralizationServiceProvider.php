<?php

namespace App\Providers;

use Illuminate\Support\Pluralizer;
use Illuminate\Support\ServiceProvider;

class PluralizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Pluralizer::useLanguage('portuguese');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
