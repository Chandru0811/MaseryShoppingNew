<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Inventory\InventoryRepository;
use App\Repositories\Inventory\EloquentInventory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InventoryRepository::class, EloquentInventory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
