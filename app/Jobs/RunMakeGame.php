<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Artisan;


class RunMakeGame extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $clicksId;
    public $gameId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($clicksId, $gameId)
    {
        $this->clicksId = $clicksId;
        $this->gameId = $gameId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        file_put_contents("/tmp/account","dude\n", FILE_APPEND );

            $exitCode = Artisan::call('clicks:play', [
                'clicksId' => $this->clicksId, 'wargame' => $this->gameId
            ]);

    }
}
