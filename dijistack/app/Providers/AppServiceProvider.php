<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\CompanyModule;

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
     View::composer('partials.sidebar', function ($view) {

        $user = auth()->user();

        if (!$user) return;

        $modules = CompanyModule::with(['module.features'])
            ->where('company_id', $user->company_id)
            ->where('status', 'Aktif')
            ->get();

        $view->with('modules', $modules);
     });
    }
}
