<?php

namespace App\Listeners;
use App\Services\CouchService;
use Auth;
use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSuccessfulLogout
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
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {

        $user = Auth::user()['name'];
        $prevDb = $this->cs->setDb('users');
        $doc = $this->cs->get('userLogins');
        $gnu = new \stdClass();
        $gnu->name = $user;
        $gnu->time = date("Y-m-d H:i:s");
        $gnu->action = "logout";
        $doc->logins[] = $gnu;
        $this->cs->put($doc->_id, $doc);
        $this->cs->setDb($prevDb);
    }
}