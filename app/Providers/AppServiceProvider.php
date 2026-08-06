<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

// 🔥 TAMBAHAN
use App\Models\Sparepart;
use App\Observers\SparepartObserver;

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
        // 🔥 PAGINATION BOOTSTRAP
        Paginator::useBootstrap();

        // 🔥 REGISTER OBSERVER
        Sparepart::observe(SparepartObserver::class);
    }
}