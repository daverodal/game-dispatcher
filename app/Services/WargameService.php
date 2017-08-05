<?php
/**
 * Created by PhpStorm.
 * User: david_rodal
 * Date: 1/15/16
 * Time: 10:13 AM
 */
namespace App\Services;
use Auth;
use \DateTime;
use GuzzleHttp\Exception\RequestException;
use Input;
use App\User;
use League\Flysystem\Exception;
use Wargame\Battle;

class  WargameService{

    static $providers = [];
    static $battleMaps = [];
    public $cs;
    static $viewBase = false;
    public $couch;
    public function __construct(CouchService $cs)
    {
        $this->cs = $cs;
        $couch = \Config::get('couch');
        $this->couch = $couch;

    }

    public static function viewBase($className){
        $base = static::$viewBase;
        $viewPath = preg_replace("/\\\\/", ".", $className);
        $viewPath = preg_replace("/\.[^.]*$/","", $viewPath);
        $viewPath = preg_replace("/^$base\./","", $viewPath);
        return $viewPath;
    }

    public static function viewParent($className){
        $child = static::viewBase($className);
        $viewArr = explode('.',$child);
        $clsName = array_pop($viewArr);
        $viewPath = implode('.', $viewArr);
        $curPath = "wargame::".implode('.',[$viewPath,$clsName]);
        $ret['clsName'] = $clsName;
        $ret['curPath'] = $curPath;
        return [$viewPath, $ret];
    }

    public static function addProvider($provider){
        static::$providers[] = $provider;
    }

    public static function addBattleMap($battleMap){
        static::$battleMaps[] = $battleMap;
    }

    public static function getBattleMaps(){
        return static::$battleMaps;
    }

    public static function getProviders(){
        return static::$providers;
    }

    public function lobbyView(){

        $user = Auth::user()['name'];

        //            $users = $this->couchsag->get('/_design/newFilter/_view/userByEmail');
//            $userids = $this->couchsag->get('/_design/newFilter/_view/userById');

//            var_dump($poll);
//            echo $this->wargame_model->getLobbyChanges(false,$poll);
        //$seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
        $this->cs->setDb('games');
        $seq = $this->cs->get("_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
        $lobbies = [];
        date_default_timezone_set("America/New_York");
        $odd = 0;

        foreach ($seq->rows as $row) {
            $keys = $row->key;
            list($creator, $gameStatus, $gameInfo, $gameName, $playerTurn, $id, $vis) = $keys;
            $id = $row->id;
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
                $playerTurn = $thePlayers[$playerTurn];
            $myTurn = "";
            if ($playerTurn == $user) {
                $playerTurn = "Your";
                $myTurn = "myTurn";
            } else {
                $playerTurn .= "'s";
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $odd ^= 1;
            $lobbies[] = array("odd" => $odd ? "odd" : "", "name" => $row->value[0], 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameInfo, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $seq = $this->cs->get("/_design/newFilter/_view/getGamesImIn?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

        $otherGames = array();
        foreach ($seq->rows as $row) {
            $keys = $row->key;
            $you = array_shift($keys);
            $creator = array_shift($keys);
            $name = array_shift($keys);
            $gameType = array_shift($keys);
            $gameClass = array_shift($keys);
            $playerStatus = array_shift($keys);
            $playerTurn = array_shift($keys);
            $filename = array_shift($keys);
            $id = $row->id;
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $myTurn = "";

            if ($playerTurn == $user) {
                $playerTurn = "Your";
                $myTurn = "myTurn";
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $otherGames[] = array("name" => $name, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $myName = $user;



//            $xml = $this->_getFeed("http://davidrodal.com/pubs/category/welcome/feed");
//            if($xml === null){
//                $item = new \stdClass();
//                $item->content = "No Content.";
//            }else {
//                $item = $xml->channel->item[0];
//                $content = $item->children('http://purl.org/rss/1.0/modules/content/');
//                $item->content = $content->encoded;
//            }

        $notFriends = User::where('id', '!=', Auth::user()->id);
        if (Auth::user()->friends->count()) {
            $notFriends->whereNotIn('id', Auth::user()->friends->modelKeys());
        }
        $notFriends = $notFriends->get();


        $item = new \stdClass();
        $item->content = "no content";
        $item->title = "Title This ";
        return compact("item", "lobbies", "otherGames", "myName", "notFriends");
    }

    public function makePrivate($docId){
        $user = Auth::user()['name'];
        $this->cs->setDb('games');
        try {
            $doc = $this->cs->get($docId);
        }catch(\Exception $e){
            if($e->getCode() === 404){
                return false;
            }
        }

        if($user !== $doc->createUser){
            return false;
        }

        $doc->visibility = "private";

        $this->cs->put($docId, $doc);
        return true;

    }

    public function makePublic($docId){
        $user = Auth::user()['name'];
        $this->cs->setDb('games');
        try {
            $doc = $this->cs->get($docId);
        }catch(\Exception $e){
            if($e->getCode() === 404){
                return false;
            }
        }

        if($user !== $doc->createUser){
            return false;
        }

        $doc->visibility = "public";

        $this->cs->put($docId, $doc);
        return true;

    }


    public function gameView($wargame){

        $user = Auth::user()['name'];
        $isAdmin = Auth::user()['is_admin'];


        $this->cs->setDb('games');
        try {
            $doc = $this->cs->get($wargame);
        }catch(\Exception $e){
            if($e->getCode() === 404){
                    return false;
            }
        }

        $name = $doc->name;
        $gameName = $doc->gameName;
        if (!$gameName) {
            return false;
        }
        if ($doc->playerStatus && $doc->playerStatus == "created") {
            return false;
        }
        $players = $doc->wargame->players;
        $player = array_search($user, $players);
        if ($player === false) {
            $player = 0;
            $visibility = $doc->visibility ?? "";
            if($visibility !== "public" && !$isAdmin){
                return false;
            }
        }
//        $this->load->library('battle');
        $units = $doc->wargame->force->units;

        $playerData = array($doc->wargame->players[$player]);
        $playersData = $doc->wargame->players;
        if (!$units) {
            $units = array();
        }
        $newUnits = array();
        foreach ($units as $aUnit) {
            $newUnit = array();
            foreach ($aUnit as $key => $value) {
                if ($key == "hexagon") {
                    continue;
                }
                if($key == "adjustments"){
                    continue;
                }
                $newUnit[$key] = $value;
            }
            $newUnit['nationality'] = $aUnit->nationality;
            $newUnit['class'] = $aUnit->class;
            $newUnit['type'] = $aUnit->nationality;
            $newUnit['unitSize'] = $aUnit->name;
            $newUnit['unitDesig'] = $aUnit->unitDesig;
            if ($aUnit->name == "infantry-1") {
                $newUnit['unitSize'] = 'xx';
            }
            if(isset($newUnit['range'])) {
                if ($newUnit['range'] == 1) {
                    $newUnit['range'] = '';
                }
            }
            $newUnits[] = $newUnit;
        }
        $units = $newUnits;
        $mapUrl = $doc->wargame->mapData->mapUrl;
        $arg = $doc->wargame->arg;
        $scenario = $doc->wargame->scenario;
        $scenarioArray = [];
        $scenarioArray[] = $scenario;
        $className = isset($doc->className)? $doc->className : '';
        $playDat = $className::getPlayerData($scenario);
        $forceName = $playDat['forceName'];
        $deployName = $playDat['deployName'];
        $docName = $name;
        $name = $gameName;
        return compact("docName","playDat", "className", "deployName", "forceName", "scenario", "scenarioArray", "name", "arg", "player", "mapUrl", "units", "playersData", "playerData", "gameName", "wargame", "user");
      }

    public function playClicks($wargame, $clicks){
        $user = Auth::user()['name'];
        $user = "Markarian";
        ini_set( 'memory_limit', 1024 . 'M' );


        foreach($clicks as $click){
          $this->doPoke($wargame, $click->event, $click->id, 0, 0, $user, $click->dieRoll);

      }
    }

    public function createWargame( $name)
    {
        $cs = $this->cs;
        date_default_timezone_set("America/New_York");
//        $data = array('docType' => "wargame", "_id" => $name, "name" => $name, "chats" => array(),"createDate"=>date("r"),"createUser"=>$this->session->userdata("user"),"playerStatus"=>"created");
//        $data = array('docType' => "wargame", "name" => $name, "chats" => array(),"createDate"=>date("r"),"createUser"=>$this->session->userdata("user"),"playerStatus"=>"created");
        try {
            $data = new \stdClass();
            $data->docType = "wargame";
            $data->name = $name;
            $data->chats = array();
            $data->createDate = date("r");
            $data->createUser = Auth::user()['name'];
            $data->playerStatus = "created";
            $cs->setDb("games");
            $ret = $cs->post($data);
        } catch (Exception $e) {
            ;
            return $e->getMessage();
        }
        return $ret;
    }

    public function getTerrainName($game, $arg, &$retTerrainDoc = false){
        $cs = $this->cs;
        try{
            $terrainName = "terrain-$game.$arg";
            $terrainDoc = $cs->get($terrainName);
        }catch(\GuzzleHttp\Exception\BadResponseException $e){}
        if(empty($terrainDoc)){
            try{
                $terrainName = "terrain-$game";
                $terrainDoc = $cs->get($terrainName);
            }catch(\GuzzleHttp\Exception\BadResponseException $e){var_dump($e->getMessage() );}
        }
        if($retTerrainDoc !== false){
            $retTerrainDoc = $terrainDoc;
        }
        return $terrainName;
    }

    public function gameUnitInit($doc, $game, $arg,  $opts){
        $doc->opts = $opts;
        $battle = Battle::battleFromName( $game, $arg, $opts);

        $cs = $this->cs;
        $cs->setDb('games');
        if (method_exists($battle, 'terrainInit')) {
            if(isset($battle->scenario->origTerrainName)){
                $terrainName = $battle->scenario->origTerrainName;
                $terrainDoc = $cs->get($terrainName);
            }else {
                $terrainDoc = null;
                $terrainName = $this->getTerrainName($game, $arg, $terrainDoc);
            }
            $battle->terrainName = $terrainName;
            $battle->terrainInit($terrainDoc);
        }
        if (method_exists($battle, 'init')) {
            $battle->init();
        }
        $doc->wargame = $battle->save();
        $click = $doc->_rev;
        $matches = array();
        preg_match("/^([0-9]+)-/", $click, $matches);
        $click = $matches[1];
        $doc->wargame->gameRules->phaseClicks[] = $click + 3;
        /* should probably get rid of this old code for genTerrain */
        if (isset($doc->wargame->genTerrain)) {
            try {
                $ter = $cs->get($doc->wargame->terrainName);
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            };
            if (empty($ter)) {
                $data = array("_id" => $doc->wargame->terrainName, "docType" => "terrain", "terrain" => $doc->wargame->terrain);
                $cs->post($data);
            } else {
                $data = array("_id" => $doc->wargame->terrainName, "docType" => "terrain", "terrain" => $doc->wargame->terrain);
                /* totally throw the old one away */

                $cs->delete($doc->wargame->terrainName, $ter->_rev);
                $cs->post($data);

            }
            unset($doc->wargame->terrain);
            $doc->wargame->genTerrain = false;

        }

        $className = $doc->className = get_class($battle);
        $doc->chats = array();
        $doc->gameName = $game;

        $doc = $cs->put($doc->_id, $doc);
        event(new \App\Events\Analytics\RecordGameEvent(['docId'=>$doc->id, 'type'=>'game-created', 'className'=> $className, 'scenario'=>$battle->scenario, 'arg'=>$battle->arg, 'time'=>time()]));

    }
    public function doPoke($wargame, $event, $id, $x, $y, $user, $dieRoll = false){
        $this->cs->setDb('games');

        $retry = 0;
        $doc = false;
        $conflictRetry = 0;
        do {
            $conflict = false;
            do {
                try {
                    $doc = $this->cs->get(urldecode($wargame));
                } catch (Exception $e) {
                    $doc = false;
                    if ($retry++ > 3) {
                        $success = false;
                        $emsg = $e->getMessage();
                        return compact('success', "emsg");
                    }
                }
            } while (!$doc);
            $ter = false;
            if (!empty($doc->wargame->terrainName)) {
                try {
                    $ter = $this->cs->get($doc->wargame->terrainName);
                } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                    var_dump($e->getMessage());
                }
                $doc->wargame->terrain = $ter->terrain;
            }
//        $this->load->library("battle");
            $game = !empty($doc->gameName) ? $doc->gameName : '';
            $emsg = false;
            $click = $doc->_rev;
            $matches = array();

            preg_match("/^([0-9]+)-/", $click, $matches);
            $click = $matches[1];
            try {
                $battle = Battle::battleFromDoc($doc);

                $isGameOver = $battle->victory->gameOver;
                $startingAttackerId = $battle->gameRules->attackingForceId;
                if ($event === SAVE_GAME_EVENT) {
                    $msg = Input::get('msg', 'defar');

                    event(new \App\Events\Params\ParamEvent(['opts' => $doc->opts, 'docType' => 'bug-report', 'type' => 'click-history', 'attackingForceId' => $startingAttackerId, 'history' => $battle->clickHistory, 'className' => $doc->className, 'arg' => $battle->arg, 'time' => time(), 'msg' => $msg]));
                    $success = true;
                    $emsg = "";
                    return compact('success', "emsg");
                }
//            $battle = Battle::getBattle($game, $doc->wargame, $doc->wargame->arg, false, $doc->className);
                if ($dieRoll !== false) {
                    $battle->combatRules->dieRoll = $dieRoll;
                }
                $doSave = $battle->poke($event, $id, $x, $y, $user, $click);
                $gameOver = $battle->victory->gameOver;
                $saveDeploy = $battle->victory->saveDeploy;
                if (!$isGameOver && $gameOver) {
                    event(new \App\Events\Analytics\RecordGameEvent(['docId' => $doc->_id, 'winner' => $battle->victory->winner, 'type' => 'game-victory', 'className' => $doc->className, 'scenario' => $battle->scenario, 'arg' => $battle->arg, 'time' => time()]));
                    event(new \App\Events\Params\ParamEvent(['opts' => $doc->opts, 'docType' => 'bug-report', 'type' => 'click-history', 'attackingForceId' => $startingAttackerId, 'history' => $battle->clickHistory, 'className' => $doc->className, 'arg' => $battle->arg, 'time' => time(), 'msg' => "Game Over Event"]));

                }
                if ($saveDeploy) {
                    /* lose nextPhase click */
                    $saveHistory = $battle->clickHistory;
                    array_pop($saveHistory);
                    event(new \App\Events\Params\ParamEvent(['opts' => $doc->opts, 'docType' => 'deploy', 'attackingForceId' => $startingAttackerId, 'history' => $saveHistory, 'className' => $doc->className, 'arg' => $battle->arg, 'time' => time()]));
                }
                $success = false;
                if ($doSave) {
                    $doc->wargame = $battle->save();

                    try {
                        $this->cs->put($doc->_id, $doc);
                        $success = true;
                    } catch (RequestException $e) {
                        $conflict = true;
                        $conflictRetry++;
                    }

                }
                if ($doSave === 0) {
                    $success = true;
                }
            } catch (Exception $e) {
                $emsg = $e->getMessage() . " \nFile: " . $e->getFile() . " \nLine: " . $e->getLine() . " \nCode: " . $e->getCode();
                $success = false;
            }

        }while($conflict && $conflictRetry < 3);
        return compact('success', "emsg");
    }



    public function fetchLobby($last_seq){
        $this->cs->setDb('games');

        $user = Auth::user()['name'];
        if (!$user) {
            header("Content-Type: application/json");
            echo json_encode(['forward'=> site_url('/users/login')]);
            return;
        }



        header("Content-Type: application/json");


//            $lastSeq = $this->wargame_model->getLobbyChanges($user, $last_seq);
        $lastSeq = $this->fetchLobbyChanges($this->cs, $user, $last_seq);


        $this->cs->setDb('games');

        $seq = $this->cs->get("_design/newFilter/_view/getLobbies?startkey=[\"$user\",\"hot seat\"]&endkey=[\"$user\",\"hot seat\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
        $lobbies = [];
        date_default_timezone_set("America/New_York");
        $odd = 0;
        foreach ($seq->rows as $row) {
            $keys = $row->key;
            $creator = array_shift($keys);
            $gameType = array_shift($keys);
            $gameName = array_shift($keys);
            $name = array_shift($keys);
            $playerTurn = array_shift($keys);
            array_shift($keys);
            $public = array_shift($keys);
            $filename = array_shift($keys);

            $id = $row->id;
            $className = $row->value[0];

            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $gameOver = $row->value[4];
            $currentTurn = $row->value[5];
            $maxTurn = $row->value[6];
            $myTurn = "";
            if ($gameOver === true) {
                $playerTurn = "Game Over";
                $myTurn = "gameOver";
            } else {

                $playerTurn = "$currentTurn of $maxTurn";
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $odd ^= 1;
            $lobbies[] = array("className" => $className, "public" => $public, "odd" => $odd ? "odd" : "", "gameName" => $gameName, "name" => $name, 'timestamp'=>$dt->getTimestamp(), 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $seq = $this->cs->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\",\"multi\"]&endkey=[\"$user\",\"multi\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

        $multiLobbies = [];
        date_default_timezone_set("America/New_York");
        $odd = 0;
        foreach ($seq->rows as $row) {
            $keys = $row->key;
            $creator = array_shift($keys);
            $gameType = array_shift($keys);
            $gameName = array_shift($keys);
            $name = array_shift($keys);
            $playerTurn = array_shift($keys);
            array_shift($keys);
            $public = array_shift($keys);
            $filename = array_shift($keys);
            $id = $row->id;
            $className = $row->value[0];

            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $gameOver = $row->value[4];

            $myTurn = "";
            if ($gameOver === true) {
                $playerTurn = "Game Over";
                $myTurn = "gameOver";
            } else {
                if ($playerTurn == $user) {
                    $playerTurn = "It's Your Turn";
                    $myTurn = "myTurn";
                } else {
                    $playerTurn = "It's " . $playerTurn . "'s Turn";
                }
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $odd ^= 1;
            $multiLobbies[] = array("className" => $className, "public" => $public, "odd" => $odd ? "odd" : "", "gameName" => $gameName, "name" => $name,'timestamp'=>$dt->getTimestamp(), 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $seq = $this->cs->get("/_design/newFilter/_view/getGamesImIn?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

        $odd = 0;
        $otherGames = array();
        foreach ($seq->rows as $row) {
            $keys = $row->key;
            $you = array_shift($keys);
            $creator = array_shift($keys);
            $name = array_shift($keys);
            $gameName = array_shift($keys);
            $oldGame = array_shift($keys);
            $gameType = array_shift($keys);
            $playerTurn = array_shift($keys);
            $filename = array_shift($keys);
            $id = $row->id;
            $className = $row->value[0];
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $gameOver = $row->value[3];
            $myTurn = "";
            if ($gameOver === true) {
                $playerTurn = "Game Over";
                $myTurn = "gameOver";
            } else {
                if ($playerTurn == $user) {
                    $playerTurn = "Your";
                    $myTurn = "myTurn";
                }
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $odd ^= 1;
            $otherGames[] = array("className" => $className, "odd" => $odd ? "odd" : "", "name" => $name, "gameName" => $gameName,'timestamp'=>$dt->getTimestamp(), 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $seq = $this->cs->get("/_design/newFilter/_view/publicGames");

        $odd = 0;
        $publicGames = array();
        foreach ($seq->rows as $row) {
            $keys = $row->key;
            $creator = array_shift($keys);
            $name = array_shift($keys);
            $gameName = array_shift($keys);
            array_shift($keys);
            $gameType = array_shift($keys);
            $playerTurn = array_shift($keys);
            $filename = array_shift($keys);
            $id = $row->id;
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $myTurn = "";
            if ($playerTurn == $user) {
                $playerTurn = "Your";
                $myTurn = "myTurn";
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $odd ^= 1;
            $publicGames[] = array("odd" => $odd ? "odd" : "", "name" => $name, "gameName" => $gameName, 'timestamp'=>$dt->getTimestamp(), 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $results = $lastSeq->results;
        $last_seq = $lastSeq->last_seq;
        return compact("lobbies", "multiLobbies", "otherGames", "last_seq", "publicGames");
    }


    public function fetchLobbyChanges(CouchService $cs, $user, $last_seq = 0, $chatsIndex = 0)
    {
        do {
            $retry = false;
            try {
                if ($last_seq) {
                    $seq = $cs->get("_changes?since=$last_seq&feed=longpoll&filter=newFilter/lobbyChanges&name=$user");
                } else {
                    $seq = $cs->get("_changes");
                }
            } catch (Exception $e) {
                $retry = true;
            }
        } while ($retry || $seq->last_seq <= $last_seq);
        return $seq;
    }

    public function grabChanges( $req,$wargame, $last_seq = 0,  $user = 'observer')
    {
        global $mode_name, $phase_name;
        $time = false;
        $timeBranch = false;
        $timeFork = false;
        if (Input::get('timeTravel')){
            $time = Input::get('timeTravel');
            if (Input::get('branch')) {
                $timeBranch = true;
            }
            if (Input::get('fork')) {
                $timeFork = true;
            }
        }

        /*
         * TODO: make this have a trip switch so it won't spin out of control if the socket is down
         */

        $this->cs->setDb('games');
        if (!$time) {
            do {
                $retry = false;
                try {
                    if ($last_seq) {
                        $seq = $this->cs->get("/_changes?since=$last_seq&feed=longpoll&filter=newFilter/namefind&name=$wargame");
                    } else {
                        $seq = $this->cs->get("/_changes");
                    }
                } catch (Exception $e) {
                    $retry = true;
                }
            } while ($retry || $seq->last_seq <= $last_seq);
            $last_seq = $seq->last_seq;
        }


//        $time = $this->session->userdata("time");
//        $time = $last_seq;
        $match = "";
//        $time = 65;
//        $time = false;
        $revision = "";
        if ($time) {
            if($time <= 2){
                return false;
            }
            $doc = $this->cs->get($wargame . "?revs_info=true");
            $revs = $doc->_revs_info;
            foreach ($revs as $k => $v) {
                if (preg_match("/^$time-/", $v->rev)) {
                    $revision = "?rev=" . $v->rev;
                    $currentRev = $doc->_rev;
                    break;
                }
            }

        }
//        file_put_contents("/tmp/perflog","\nGetting ".microtime(),FILE_APPEND);
        $doc = $this->cs->get($wargame . $revision);
        if ($timeBranch) {
            $doc->_rev = $currentRev;
            $doc->wargame->gameRules->flashMessages[] = "Time Travel to click $time";
            $this->cs->put($doc->_id, $doc);
            $last_seq = 0;
        }

        if($timeFork){
            $doc->wargame->gameRules->flashMessages[] = "Time Travel to click $time";
            $doc->forkedFrom = $doc->_id;
            unset($doc->_id);
            unset($doc->_rev);
            $doc->name .= "Clone $time";
            $ret = $this->cs->post($doc);
            $last_seq = 0;

            if (is_object($ret) === true) {
                $wargame = $ret->id;
//                $req->session()->put("wargame", $wargame);
            }
            $doc = $this->cs->get($wargame);

        }

        $class = $doc->className;
        return $class::transformChanges($doc, $last_seq, $user);
    }

    public function enterMulti($wargame, $playerOne, $playerTwo, $visibility, $playerThree, $playerFour)
    {
        $user = Auth::user()['name'];
        $this->cs->setDb('games');
        try {
            $doc = $this->cs->get($wargame);
        } catch (Exception $e) {
            return false;
        }
        if (!$doc || $user != $doc->createUser) {
            return false;
        }
        $doc->playerStatus = "multi";
        $doc->visibility = $visibility;
        $doc->wargame->players = array("", $playerOne, $playerTwo);
        if($playerThree){
            $doc->wargame->players[] = $playerThree;
            if($playerFour){
                $doc->wargame->players[] = $playerFour;
            }
        }
        $doc->wargame->gameRules->turnChange = true;
        $this->cs->put($doc->_id, $doc);
        event(new \App\Events\Analytics\RecordGameEvent(['docId'=>$doc->_id, 'type'=>'multi-entered', 'className'=> $doc->className, 'scenario'=>$doc->wargame->scenario, 'arg'=>$doc->wargame->arg, 'time'=>time()]));
        return true;
    }

    public function saveTerrainDoc($terrainDocName, $wargameDoc){
        $ter = false;
        $prevDb = $this->cs->setDb('games');
        try {
            $ter = $this->cs->get($terrainDocName);
        } catch (\GuzzleHttp\Exception\BadResponseException  $e) {
        };
        if (!$ter) {
            $data = array("_id" => $terrainDocName, "docType" => "terrain", "terrain" => $wargameDoc->terrain);
            $this->cs->post($data);
        } else {

            $data = array("_id" => $terrainDocName, "docType" => "terrain", "terrain" => $wargameDoc->terrain);
            /* totally throw the old one away */

            $this->cs->delete($terrainDocName, $ter->_rev);
            $retry = 0;
            $done = false;
            do {
                try {
                    $this->cs->post($data);
                    $done = true;
                } catch (\Exception $e) {
                    $retry++;
                    if($e->getCode() !== 409){
                        throw($e);
                    }
                    if($retry >= 3){
                        throw($e);
                    }
                }
            }while(!$done);
        }
        $prevDb = $this->cs->setDb($prevDb);
    }


    public function rotateImage($filename, $dir = false)
    {

// Get new dimensions
        $imageArray = @getimagesize($filename);
        if(!is_array($imageArray )){
            echo "Cannot rotate image\n";
            return;
        }
        list($width, $height, $type) = $imageArray;

// Resample
        switch($type){
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filename);
                break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filename);
                break;
        }
        $rotate = imagerotate($image, 90, 0);

// Output
        $f = basename($filename,'.png'). "Left.png";
        if($dir){
            $f = "$dir/$f";
        }
        imagepng($rotate, "js/$f");
        imagedestroy($rotate);

        $f = basename($filename,'.png'). "Right.png";
        if($dir){
            $f = "$dir/$f";
        }
        $rotate = imagerotate($image, -90, 0);
        imagepng($rotate, "js/$f");
        imagedestroy($image);
        imagedestroy($rotate);
    }

    public function resizeImage($filename, $new_width = 500, $dir = 'smallImages')
    {
        ini_set( 'memory_limit', 1024 . 'M' );

// Get new dimensions
        list($width, $height, $type) = getimagesize($filename);
        $new_height = ($height / $width) * $new_width;

// Resample
        $image_p = imagecreatetruecolor($new_width, $new_height);
        switch($type){
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filename);
                break;
            case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($filename);
                break;
        }
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

// Output
        $f = "js/$dir/".basename($filename,'.png'). ".png";
        imagepng($image_p, "$f");
        imagedestroy($image_p);
        imagedestroy($image);

        $url = config('publish.hostname'). "/$f";
        return $url;

    }
}
