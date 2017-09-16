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


class ClicksPlay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clicks:play {--list} {--byDeploy} {--gameClicks} {clicksId?} {wargame?} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play Clicks to a game.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( CouchService $cs, WargameService $ws, WargameController $wc)
    {
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

        $list = $this->option('list');
        $gameClicks = $this->option('gameClicks');
        if($list){
            $this->cs->setDb('params');
            $viewName = "_design/paramEvents/_view/byClassName?reduce=false";
            $paramObj = $this->cs->get($viewName);
            foreach($paramObj->rows as $row){
                echo $row->id." ".$row->key[0]." ". $row->key[1]. "\n";
            }
            return;
        }
        $this->cs->setDb('params');

        $wargame = $this->argument('wargame');

        $byDeploy = $this->option('byDeploy');

        $clicksId = $this->argument('clicksId');


        $this->info('starting');
        if($gameClicks){
            $this->cs->setDb('games');
            $paramObj = $this->cs->get($clicksId);
            $this->ws->playClicks($wargame, $paramObj->wargame->clickHistory);
            $this->info('Completed');
        }else{
            $paramObj = $this->cs->get($clicksId);
            $history = $paramObj->history;
            if($byDeploy){
                $oldHistory = $paramObj->history;
                $newHistory = [];
                foreach($oldHistory as $current){
                    if($paramObj->attackingForceId === $current->playerId){
                        $newHistory[] = $current;
                    }
                }
                $history = $newHistory;
            }
            $this->ws->playClicks($wargame, $history);
        }

        return "xyzzy";

    }
}
