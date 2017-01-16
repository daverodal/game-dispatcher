<?php

namespace App\Listeners;
use App\Services\CouchService;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSuccessfulLogin
{
    public $cs;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(CouchService $cs, Request $req)
    {
        $this->cs = $cs;
//        $req->session()->put('wargame', '');
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = Auth::user()['name'];


        $prevDb = $this->cs->setDb('users');
        $doc = $this->cs->get('userLogins');

        $gnu = new \stdClass();
        $gnu->name = $user;
        date_default_timezone_set("America/New_York");
        $gnu->time = date("D M jS Y, g:i:s a T");
        $gnu->action = "login";
        $doc->logins[] = $gnu;
        $this->cs->put($doc->_id, $doc);
        $this->cs->setDb($prevDb);

    }
}
