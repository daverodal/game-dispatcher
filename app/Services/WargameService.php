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
use Input;

class  WargameService{
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
    public function lobbyView(){

        $user = Auth::user()['name'];

        //            $users = $this->couchsag->get('/_design/newFilter/_view/userByEmail');
//            $userids = $this->couchsag->get('/_design/newFilter/_view/userById');

//            var_dump($poll);
//            echo $this->wargame_model->getLobbyChanges(false,$poll);
        //$seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
        $this->cs->setDb('mydatabase');
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
        $item = new \stdClass();
        $item->content = "no content";
        $item->title = "Title This ";
        return compact("item", "lobbies", "otherGames", "myName");
    }

    public function gameView($wargame){

        $user = Auth::user()['name'];


        $this->cs->setDb('mydatabase');
        $doc = $this->cs->get($wargame);

        $name = $doc->name;
        $gameName = $doc->gameName;
        if (!$gameName) {
            return redirect("/wargame/unattached-game/");
        }
        if ($doc->playerStatus && $doc->playerStatus == "created") {
            redirect("/wargame/play-as");
        }
        $players = $doc->wargame->players;
        $player = array_search($user, $players);
        if ($player === false) {
            $player = 0;
        }
//        $this->load->library('battle');
        $units = $doc->wargame->force->units;

        $playerData = array($doc->wargame->players[$player]);
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
            $newUnit['class'] = $aUnit->nationality;
            $newUnit['type'] = $aUnit->class;
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
        return compact("className", "deployName", "forceName", "scenario", "scenarioArray", "name", "arg", "player", "mapUrl", "units", "playerData", "gameName", "wargame", "user");
      }


    public function fetchLobby($last_seq){
        $this->cs->setDb('mydatabase');

        $user = Auth::user()['name'];
        if (!$user) {
            header("Content-Type: application/json");
            echo json_encode(['forward'=> site_url('/users/login')]);
            return;
        }



        header("Content-Type: application/json");


//            $lastSeq = $this->wargame_model->getLobbyChanges($user, $last_seq);
        $lastSeq = $this->fetchLobbyChanges($this->cs, $user, $last_seq);


        $this->cs->setDb('mydatabase');

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
            $lobbies[] = array("public" => $public, "odd" => $odd ? "odd" : "", "gameName" => $gameName, "name" => $name, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
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
            $multiLobbies[] = array("public" => $public, "odd" => $odd ? "odd" : "", "gameName" => $gameName, "name" => $name, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
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
            $otherGames[] = array("odd" => $odd ? "odd" : "", "name" => $name, "gameName" => $gameName, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
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
            $publicGames[] = array("odd" => $odd ? "odd" : "", "name" => $name, "gameName" => $gameName, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $results = $lastSeq->results;
        $last_seq = $lastSeq->last_seq;
        return compact("lobbies", "multiLobbies", "otherGames", "last_seq", "results", "publicGames");
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

        $this->cs->setDb('mydatabase');
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
                $wargame = $ret->body->id;
                $req->session()->put("wargame", $wargame);
            }
            $doc = $this->cs->get($wargame);

        }

        $class = $doc->className;
        return $class::transformChanges($doc, $last_seq, $user);
    }

    public function enterMulti($wargame, $playerOne, $playerTwo, $visibility, $playerThree, $playerFour)
    {
        $user = Auth::user()['name'];
        $this->cs->setDb('mydatabase');
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
        return true;
    }

    public function saveTerrainDoc($terrainDocName, $wargameDoc){
        $ter = false;
        $prevDb = $this->cs->setDb('mydatabase');
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
            $this->cs->post($data);
        }
        $prevDb = $this->cs->setDb($prevDb);
    }


    public function rotateImage($filename, $dir = false)
    {

// Get new dimensions
        list($width, $height, $type) = getimagesize($filename);

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
        $f = "$dir/".basename($filename,'.png'). ".png";
        imagepng($image_p, "js/$f");
        imagedestroy($image_p);
        imagedestroy($image);

        return dirname($filename)."/".$f;

    }
}
