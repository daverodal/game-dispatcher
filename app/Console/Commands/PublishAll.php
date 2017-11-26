<?php

namespace App\Console\Commands;
use League\Flysystem\Exception;
use Psy\Exception\ErrorException;
use Storage;

use App\Services\WargameService;
use Illuminate\Console\Command;
use App\Services\CouchService;
use App\Http\Controllers\MapsController;
use App\Http\Controllers\WargameController;

class PublishAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:all {continueFrom?} {continueFromScenario?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all maps.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MapsController $mc, CouchService $cs, WargameService $ws, WargameController $wc)
    {
        $this->mc = $mc;
        $this->cs = $cs;
        $this->ws = $ws;
        $this->wc = $wc;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $continueFrom = $this->argument('continueFrom');
        $continueFromScenario = $this->argument(('continueFromScenario'));

        $maps = $this->mc->fetchMaps($this->cs);
        chdir('public');
        $pickup = false;
        if(!$continueFrom){
            $pickup = true;
        }

        foreach($maps['maps'] as $map){
            if($map->gameName !== $continueFrom &&  !$pickup){
                continue;
                if($continueFromScenario && $map->scenarioName !== $continueFromScenario){
                    continue;
                }
            }
            $pickup = true;
            $this->info($map->gameName." ".$map->scenarioName);
            if(trim($map->gameName) == false){
                continue;
            }
            $this->wc->terrainInit($this->cs, $this->ws, $map->gameName, $map->scenarioName, $map->hexStr);
        }
    }
}
