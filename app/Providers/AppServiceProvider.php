<?php

namespace App\Providers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($url = env('DATABASE_URL')) {
            $components = parse_url($url);
            config([
                'database.connections.mysql.host' => $components['host'],
                'database.connections.mysql.database' => ltrim($components['path'], '/'),
                'database.connections.mysql.username' => $components['user'],
                'database.connections.mysql.password' => $components['pass'],
            ]);
        }
    }
}
