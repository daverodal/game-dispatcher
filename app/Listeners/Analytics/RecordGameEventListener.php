<?php

namespace App\Listeners\Analytics;

use App\Events\Analytics\RecordGameEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\CouchService;

class RecordGameEventListener
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
    public function handle(RecordGameEvent $event)
    {
        $prevDb = $this->cs->setDb('analytics');
        $this->cs->post($event->data);
        $this->cs->setDb($prevDb);
    }
}
