<?php

namespace App\Providers;

use App\Services\AdminService;
use App\Services\CouchService;
use Illuminate\Support\ServiceProvider;

class BattleProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(AdminService $ad)
    {
        \Wargame\Battle::$ad = $ad;
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {



    }
}
