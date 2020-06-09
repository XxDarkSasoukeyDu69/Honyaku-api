<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  //  use Laravel\Cashier\Cashier;
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       // Cashier::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
