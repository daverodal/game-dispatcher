<?php

namespace App\Listeners;
use App\Services\CouchService;

use Auth;
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
    public function __construct(CouchService $cs)
    {
        $this->cs = $cs;
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
        $gnu->time = date("Y-m-d H:i:s");
        $gnu->action = "login";
        $doc->logins[] = $gnu;
        $this->cs->put($doc->_id, $doc);
        $this->cs->setDb($prevDb);

    }
}
