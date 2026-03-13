<?php

use App\Providers\AppServiceProvider;
use App\Providers\MigrationServiceProvider;
use App\Providers\PluralizationServiceProvider;
use App\Providers\ResetPasswordProvider;
use App\Providers\TelescopeServiceProvider;
use Tymon\JWTAuth\Providers\LaravelServiceProvider;

return [
    AppServiceProvider::class,
    MigrationServiceProvider::class,
    PluralizationServiceProvider::class,
    ResetPasswordProvider::class,
    // App\Providers\SeedersServiceProvider::class,
    TelescopeServiceProvider::class,
    LaravelServiceProvider::class,
];
