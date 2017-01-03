<?php

namespace App\Listeners\Params;

use App\Events\Params\ParamEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\CouchService;

class ParamEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public $cs;
    public function __construct(CouchService $cs)
    {
        $this->cs = $cs;
    }

    /**
     * Handle the event.
     *
     * @param  RecordGameEvent  $event
     * @return void
     */
    public function handle(ParamEvent $event)
    {
        $prevDb = $this->cs->setDb('params');
        $this->cs->post($event->data);
        $this->cs->setDb($prevDb);
    }
}
