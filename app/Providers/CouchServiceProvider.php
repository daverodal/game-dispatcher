<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CouchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Services\CouchService',function(){
            $guz = new \App\Services\CouchService([
                // Base URI is used with relative requests
                'base_uri' => "http://pink:floyd@localhost:5984/",
                // You can set any number of default request options.
                'timeout'  => 1800.0,
            ]);
            return $guz;
        });
    }
}
