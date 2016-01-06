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
            $conf = $this->app->config['couch'];
            $user = $conf['username'];
            $pass = $conf['password'];
            $host = $conf['hostname'];
            $port = $conf['port'];
            $login = $user ? "$user:$pass@" : "";
            $guz = new \App\Services\CouchService([
                // Base URI is used with relative requests
                'base_uri' => "http://$login$host:$port/",
                // You can set any number of default request options.
                'timeout'  => 1800.0,
            ]);
            $guz->setDb($conf['restName']);
            return $guz;
        });
    }
}
