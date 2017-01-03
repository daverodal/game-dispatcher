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

use Auth;
use User;

class ClicksPlay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clicks:play {--list} {--gameClicks} {wargame?} {clicksId?}';

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
    public function __construct( CouchService $cs, WargameService $ws, WargameController $wc, Auth $au)
    {
        $this->cs = $cs;
        $this->ws = $ws;
        $this->wc = $wc;
        $this->au = $au;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        dd(new User());

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
        $clicksId = "7ac90438df5c9435246dda967f2b909b";

        $wargame = $this->argument('wargame');

        $clicksId = $this->argument('clicksId');
        if($gameClicks){
            $this->cs->setDb('games');
            $paramObj = $this->cs->get($clicksId);
            $this->ws->playClicks($wargame, $paramObj->wargame->clickHistory);
        }else{
            $paramObj = $this->cs->get($clicksId);
            $this->ws->playClicks($wargame, $paramObj->history);
        }

    }
}
