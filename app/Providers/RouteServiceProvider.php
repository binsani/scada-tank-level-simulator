<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    public function boot(): void
    {
        $this->routes(function (): void {
            require base_path('routes/api.php');
            require base_path('routes/web.php');
        });
    }
}
