<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

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
        View::composer('layouts.public', function ($view) {
            $view->with([
                'siteName' => Setting::getValue('school_name') ?? 'Sistem Informasi Madrasah',
                'siteLogo' => Setting::getValue('school_logo_path'),
                'navPages' => Page::published()->orderBy('sort_order')->get(['title', 'slug']),
            ]);
        });
    }
}
