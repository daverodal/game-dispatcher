<?php

namespace App\Console\Commands;
Use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PopulateTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate API Tokens';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $all = User::all();
        foreach($all as $user){
            echo "Name ". $user->name;

            echo " Token ". $user->api_token. "\n";
            $user->api_token =  Str::random(80);
            echo " Token ". $user->api_token. "\n";
            $user->save();
        }
//        var_dump($user->api_token);
//        var_dump(User::first());
    }
}
