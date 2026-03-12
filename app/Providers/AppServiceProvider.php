<?php

namespace App\Providers;

use App\Domains\Shared\Macros\BelongsToManyCreateUpdateOrDelete;
use App\Domains\Shared\Macros\CreateUpdateOrDelete;
use App\Domains\Pedido\Events\NewOrderReceived;
use App\Domains\Pedido\Listeners\SendNewOrderNotification;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceHttps(config('pp.url'));
        Schema::defaultStringLength(191);

        Event::listen(NewOrderReceived::class, SendNewOrderNotification::class);

        HasMany::macro('createUpdateOrDelete', function (iterable $records) {
            /** @var HasMany $hasMany */
            $hasMany = $this;

            return (new CreateUpdateOrDelete($hasMany, $records))();
        });

        BelongsToMany::macro('createUpdateOrDeletePivot', function (iterable $records, array $pivotAttributes = []) {
            /** @var BelongsToMany $relation */
            $relation = $this;

            return (new BelongsToManyCreateUpdateOrDelete($relation, $records, $pivotAttributes))();
        });
    }
}
