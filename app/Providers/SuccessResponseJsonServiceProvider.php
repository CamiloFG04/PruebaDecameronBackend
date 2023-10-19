<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SuccessResponseJsonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        require_once app_path() . '/Helpers/SuccessResponseJson.php';
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
