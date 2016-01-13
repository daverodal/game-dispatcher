<?php
/**
 * Created by PhpStorm.
 * User: david_rodal
 * Date: 1/7/16
 * Time: 2:49 PM
 */

namespace app\Http\Controllers;
use App\Services\AdminService;
use Illuminate\Http\Request;
use App\Services\CouchService;
use Auth;
use Input;
use \DateTime;
use \Wargame\Battle;


class WargameController extends Controller
{

    public function getIndex(){
        return 'love';
    }

    public function getPlay(Request $req, CouchService $cs){

        $wargame = $req->session()->get('wargame');
        $user = Auth::user()['name'];


        if (!$wargame) {
//            $users = $this->couchsag->get('/_design/newFilter/_view/userByEmail');
//            $userids = $this->couchsag->get('/_design/newFilter/_view/userById');

//            var_dump($poll);
//            echo $this->wargame_model->getLobbyChanges(false,$poll);
            //$seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
            $cs->setDb('mydatabase');
            $seq = $cs->get("_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
            $lobbies = [];
            date_default_timezone_set("America/New_York");
            $odd = 0;

            foreach ($seq->rows as $row) {
                $keys = $row->key;
                $creator = array_shift($keys);
                $name = array_shift($keys);
                $gameType = array_shift($keys);
                $playerTurn = array_shift($keys);
                $filename = array_shift($keys);
//               $key = implode($keys,"  ");
                $id = $row->id;
                $dt = new DateTime($row->value[1]);
                $thePlayers = $row->value[2];
//                $playerTurn = $thePlayers[$playerTurn];
                $myTurn = "";
                $playerTurn = "Markarian";
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
                $lobbies[] = array("odd" => $odd ? "odd" : "", "name" => $row->value[0], 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
            }
            $seq = $cs->get("/_design/newFilter/_view/getGamesImIn?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

            $otherGames = array();
            foreach ($seq->rows as $row) {
                $keys = $row->key;
                $you = array_shift($keys);
                $creator = array_shift($keys);
                $name = array_shift($keys);
                $gameType = array_shift($keys);
                $playerTurn = array_shift($keys);
                $filename = array_shift($keys);
                $id = $row->id;
                $dt = new DateTime($row->value[1]);
                $thePlayers = $row->value[2];
                $playerTurn = $thePlayers[$playerTurn];
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
            return view("wargame/lobby", compact("item", "lobbies", "otherGames", "myName"));

        }





        $cs->setDb('mydatabase');
        $doc = $cs->get($wargame);

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
            if ($newUnit['range'] == 1) {
                $newUnit['range'] = '';
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
        $playDat = $className::getPlayerData();
        $forceName = $playDat['forceName'];
        $deployName = $playDat['deployName'];
        $viewPath = preg_replace("/\\\\/", ".", $className);
        $viewPath = preg_replace("/\.[^.]*$/","", $viewPath).".view";
        return view("wargame::$viewPath", compact("deployName", "forceName", "scenario", "scenarioArray", "name", "arg", "player", "mapUrl", "units", "playerData", "gameName", "wargame", "user"));
    }



        public function anyFetchLobby(Request $req, CouchService $cs, $last_seq = '')
        {

            $cs->setDb('mydatabase');

            $user = Auth::user()['name'];
            if (!$user) {
                header("Content-Type: application/json");
                echo json_encode(['forward'=> site_url('/users/login')]);
                return;
            }
//            $this->load->helper('date');
            $wargame = $req->session()->get('wargame');


            header("Content-Type: application/json");
//            $this->load->model("wargame/wargame_model");


//            $lastSeq = $this->wargame_model->getLobbyChanges($user, $last_seq);
            $lastSeq = $this->fetchLobbyChanges($cs, $user, $last_seq);


            $cs->setDb("mydatabase");

            $seq = $cs->get("_design/newFilter/_view/getLobbies?startkey=[\"$user\",\"hot seat\"]&endkey=[\"$user\",\"hot seat\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
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
            $seq = $cs->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\",\"multi\"]&endkey=[\"$user\",\"multi\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

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
            $seq = $cs->get("/_design/newFilter/_view/getGamesImIn?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

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
            $seq = $cs->get("/_design/newFilter/_view/publicGames");

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
            $ret = compact("lobbies", "multiLobbies", "otherGames", "last_seq", "results", "publicGames");
            return $ret;
            return Response::json($ret);
            return json_encode($ret);
        }


    public function getSetplay(Request $req, $id){
        $req->session()->put('wargame', $id);
        return redirect('wargame/play');
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





    function getUnattachedGame(AdminService $ad, CouchService $cs, Request $req, $dir = false, $genre = false, $game = false, $theScenario = false)
    {

        $gamesAvail = $ad->getAvailGames($dir, $genre, $game);
        $plainGenre = rawurldecode($genre);
        $cs->setDb("mydatabase");
        $seq = $cs->get("_design/newFilter/_view/getLobbies");
        $games = array();
        $theGame = false;
        $siteUrl = url("wargame/unattached-game/");
        $editor = false;
        if($req->session()->get("editor")){
            $editor = true;
        }


        $backgroundImage = "Egyptian_Pharaoh_in_a_War-Chariot,_Warrior,_and_Horses._(1884)_-_TIMEA.jpg";
        $backgroundAttr = 'By Unknown author [<a href="http://creativecommons.org/licenses/by-sa/2.5">CC BY-SA 2.5</a>], <a href="http://commons.wikimedia.org/wiki/File%3AEgyptian_Pharaoh_in_a_War-Chariot%2C_Warrior%2C_and_Horses._(1884)_-_TIMEA.jpg">via Wikimedia Commons</a>';
        if($genre){
            if(preg_match("/18%27th/", $genre)){
                $backgroundImage = "18th_century_gun.jpg";
                $backgroundAttr = 'By MKFI (Own work) [Public domain], <a href="http://commons.wikimedia.org/wiki/File%3ASwedish_18th_century_6_pound_cannon_front.JPG">via Wikimedia Commons</a>';
            }

            if(preg_match("/Americas/", $genre)){
                $backgroundImage = "Yorktown80.jpg";
                $backgroundAttr = 'John Trumbull [Public domain], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3AYorktown80.JPG">via Wikimedia Commons</a>';
            }

            if(preg_match("/Napoleonic/", $genre)){
                $backgroundImage = "Napoleon.jpg";
                $backgroundAttr = 'Jacques-Louis David [Public domain], <a href="https://commons.wikimedia.org/wiki/File%3AJacques-Louis_David_-_Napoleon_at_the_St._Bernard_Pass_-_WGA06083.jpg">via Wikimedia Commons</a>';
            }

            if(preg_match("/19%27th/", $genre)) {
                if(preg_match("/Europe/", $genre)){
                    $backgroundImage = "Grande_Armée_-_10th_Regiment_of_Cuirassiers_-_Colonel.jpg";
                    $backgroundAttr = 'By Carle Vernet (Carle Vernet, La Grande Armée de 1812) [Public domain], <a target="blank" href="https://commons.wikimedia.org/wiki/File%3AGrande_Arm%C3%A9e_-_10th_Regiment_of_Cuirassiers_-_Colonel.jpg">via Wikimedia Commons</a>';
                }else{
                    $backgroundImage = "1280px-1864_Johnson_s_Map_of_India_(Hindostan_or_British_India)_-_Geographicus_-_India-j-64.jpg";
                    $backgroundAttr = 'Alvin Jewett Johnson [Public domain], <a target="blank" href="http://commons.wikimedia.org/wiki/File%3A1864_Johnson&#039;s_Map_of_India_(Hindostan_or_British_India)_-_Geographicus_-_India-j-64.jpg">via Wikimedia Commons</a>';
                }
            }
            if(preg_match("/20'th/", $genre)){
                $backgroundImage = "M110_howitzer.jpg";
                $backgroundAttr = 'By Greg Goebel [Public domain], <a target="blank" href="http://commons.wikimedia.org/wiki/File%3AM110_8_inch_self_propelled_howitzer_tank_military.jpg">via Wikimedia Commons</a>';
            }

            if(preg_match("/early/", $genre)){
                $backgroundImage = "French-Marne-Machinegun.jpg";
                $backgroundAttr = 'By Unknown or not provided (U.S. National Archives and Records Administration) [Public domain], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3AFrench_troopers_under_General_Gouraud%2C_with_their_machine_guns_amongst_the_ruins_of_a_cathedral_near_the_Marne..._-_NARA_-_533679.tif">via Wikimedia Commons</a>';
            }
        }

        if ($game !== false) {
            $terrainName = "terrain-".$game;
            if($theScenario){
                $terrainName .= ".$theScenario";
            }
            try {
                $terrain = $cs->get($terrainName);
            }catch(\GuzzleHttp\Exception\BadResponseException $e){echo $terrainName." ".$e->getMessage();               }
            if(!$terrain){
                $terrain = $this->couchsag->get("terrain-".$game);
            }

            $bigMapUrl = $mapUrl = $terrain->terrain->mapUrl;
            if(isset($terrain->terrain->smallMapUrl)){
                $mapUrl = $terrain->terrain->smallMapUrl;
            }

            $theGame = $gamesAvail[0];
            $theScenarios = [];
            foreach($theGame->value->scenarios as $theScenario => $scenario){
                $terrainName = "terrain-".$game;
                if($theScenario){
                    $terrainName .= ".$theScenario";
                }
                try {
                    $terrain = $cs->get($terrainName);
                }catch(\GuzzleHttp\Exception\BadResponseException $e){}
                if(!$terrain){
                    $terrain = $this->couchsag->get("terrain-".$game);
                }

                $thisScenario = $theGame->value->scenarios->$theScenario;
                $thisScenario->sName = $theScenario;
                $thisScenario->mapUrl = $terrain->terrain->mapUrl;
                $theGame->value->scenarios->$theScenario->mapUrl = $terrain->terrain->mapUrl;
                $thisScenario->bigMapUrl = $terrain->terrain->mapUrl;
                if(isset($terrain->terrain->smallMapUrl)){
                    $thisScenario->mapUrl  = $terrain->terrain->smallMapUrl;
                }
                $theScenarios[] = $thisScenario;

            }
            $customScenarios = $ad->getCustomScenarios($dir, $genre, $game);
            foreach($customScenarios as $customScenario){
                $terrainName = "terrain-".$game;
                $theScenario = $customScenario->scenario;
                if($theScenario){
                    $terrainName .= ".$theScenario";
                }
                try {
                    $terrain = $this->couchsag->get($terrainName);
                }catch(\GuzzleHttp\Exception\BadResponseException $e){}
                if(!$terrain){
                    $terrain = $this->couchsag->get("terrain-".$game);
                }
                $thisScenario = $customScenario->value;
                $thisScenario->sName = $theScenario;
                $thisScenario->mapUrl = $terrain->terrain->mapUrl;
//                $theGame->value->scenarios->$theScenario->mapUrl = $terrain->terrain->mapUrl;
                $thisScenario->bigMapUrl = $terrain->terrain->mapUrl;
                if(isset($terrain->terrain->smallMapUrl)){
                    $thisScenario->mapUrl  = $terrain->terrain->smallMapUrl;
                }
                $theScenarios[] = $thisScenario;
            }

//            $theGame->value->scenarios = $theScenarios;

            $gameFeed = strtolower($game);
            $feed = file_get_contents("http://davidrodal.com/pubs/category/$gameFeed/feed");
            $theGameMeta = (array)$theGame->value;
            $theGameMeta['options'] = isset($theGameMeta['options'])? $theGameMeta['options'] : [];
            unset($theGameMeta->scenarios);
            if ($feed !== false) {
                $xml = new \SimpleXmlElement($feed);

                foreach ($xml->channel->item as $entry) {
                    if (preg_match("/Historical/", $entry->title)) {
                        $matches = [];
                        preg_match("/p=(\d+)$/",$entry->guid,$matches);
                        $editLink = "http://davidrodal.com/pubs/wp-admin/post.php?post=".$matches[1]."&action=edit";
                        $content = $entry->children('http://purl.org/rss/1.0/modules/content/');
                        $str = $content->encoded;
                        // http://stackoverflow.com/questions/8781911/remove-non-ascii-characters-from-string-in-php
                        $str = preg_replace('/[[:^print:]]/', '', $str); // should be aA
                        $str = preg_replace("/></","> <", $str);

//                        $theGame->value->longDesc = $str;
//                        $theGame->value->histEditLink = $editLink;
                        $theGameMeta['longDesc'] = $str;
                        $theGameMeta['histEditLink'] = $editLink;
                    }
                    if (preg_match("/Player/", $entry->title)) {
                        $content = $entry->children('http://purl.org/rss/1.0/modules/content/');
                        $str = $content->encoded;

                        // http://stackoverflow.com/questions/8781911/remove-non-ascii-characters-from-string-in-php
                        $str = preg_replace('/[[:^print:]]/', '', $str); // should be aA
                        $matches = [];
                        if(preg_match("/p=(\d+)$/",$entry->guid,$matches)){
                            $editLink = "http://davidrodal.com/pubs/wp-admin/post.php?post=".$matches[1]."&action=edit";
//                            $theGame->value->playerEditLink = "<a target='blank' href='$editLink'>edit</a>";
                            $theGameMeta['playerEditLink'] = "<a target='blank' href='$editLink'>edit</a>";
                        }
                        $theGameMeta['playerNotes'] = $str;
                        $theGame->value->playerNotes = $str;

                    }
                }
            }
            unset($theGame->value);
            $theGame = (array)$theGame;
            $theGameMeta['description'] = '';

            return view("wargame/wargame-unattached-game", compact("theScenarios", "editor", "backgroundImage", "backgroundAttr","bigMapUrl", "mapUrl", "theScenario", "plainGenre", "theGame", "games", "nest","siteUrl","theGameMeta"));
        } else {
            foreach ($gamesAvail as $gameAvail) {
                if($gameAvail->game) {
                    $terrainName = "terrain-" . $gameAvail->game;
                    $terrain = "";
                    try {
                        $terrain = $cs->get($terrainName);
                    } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                        continue;
                        echo "EXCEPTION $terrainName ";
                    }

                    $mapUrl = $terrain->terrain->mapUrl;
                    if (isset($terrain->terrain->smallMapUrl)) {
                        $mapUrl = $terrain->terrain->smallMapUrl;
                    }
                    $gameAvail->maxCol = $terrain->terrain->maxCol;
                    $gameAvail->maxRow = $terrain->terrain->maxRow;

                    $gameAvail->name = $gameAvail->value->name;
                    $gameAvail->mapUrl = $mapUrl;
                    $gameAvail->longDescription = isset($gameAvail->value->longDescription) ? $gameAvail->value->longDescription :'';
                    $gameAvail->description = isset($gameAvail->value->description) ? $gameAvail->value->description : '';
                }

                $gameAvail->urlGenre = rawurlencode($gameAvail->genre);
                if(!is_numeric($gameAvail->value)){
                    $nScenarios = count((array)$gameAvail->value->scenarios);
                    unset($gameAvail->value);
                    $gameAvail->value = $nScenarios;
                }

                $games[] = $gameAvail;

            }
            $theScenarios = [];
            $theGameMeta = [];
            $theGameMeta['description'] = '';
            $theGameMeta['name'] = '';
            $theGameMeta['histEditLink'] = '';
            $theGameMeta['options'] = [];
            if(count($games) > 0 && !empty($games[0]->game)){
                return view("wargame/wargame-unattached-games", compact("theScenarios", "theGameMeta", "editor", "backgroundAttr", "backgroundImage","theScenario", "plainGenre", "theGame", "games", "nest","siteUrl"));

            }

            return view("wargame/wargame-unattached", compact("theScenarios", "theGameMeta", "editor", "backgroundAttr", "backgroundImage","theScenario", "plainGenre", "theGame", "games", "nest","siteUrl"));
        }
//        echo "<pre>"; var_dump(compact("mapUrl","theScenario", "plainGenre", "theGame", "games", "nest","siteUrl"));die('did');


    }

    public function postCreateWargame(CouchService $cs, Request $req,$game, $scenario)
    {

        $message = "";
        $wargame = Input::get('wargame');
        if ($wargame) {
            $ret = $this->createWargame($cs, $wargame);
            if (is_object($ret) === true) {
                $req->session()->put("wargame" , $ret->id);
                $opts = "";

                foreach($_GET as $k => $v){
                    $opts .= "$k=$v&";
                }
                return redirect("/wargame/unit-init/$game/$scenario?$opts");
            }
            $message = "Name $wargame already used, please enter new name";
        }
        if ($this->input->post()) {
            $message = "Please in put a name (need not be unique)";
        }
        $this->load->view("wargame/wargameCreate", compact("message", "game","scenario"));
    }

    public function createWargame(CouchService $cs, $name)
    {
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
            $cs->setDb("mydatabase");
            $ret = $cs->post($data);
        } catch (Exception $e) {
            ;
            return $e->getMessage();
        }
        return $ret;
    }

    function getLeaveGame(Request $req)
    {
        $req->session()->forget('wargame');
        return redirect("/wargame/play");
    }

    public function getUnitInit( Request $req,CouchService $cs, AdminService $ad, $game, $arg = false)
    {
        $user = Auth::user()['name'];
        $wargame = urldecode($req->session()->get("wargame"));
        $chat = Input::get('chat', TRUE);
        $cs->setDb('mydatabase');
        $doc = $cs->get(urldecode($wargame));
        if ($user != $doc->createUser) {
            return redirect("wargame/play");
        }


        $opts = [];
        foreach($_GET as $k=>$v){
            $opts[] = $k;
        }

//        $this->load->model('users/users_model');
        $battle = Battle::getBattle( $game, null, $arg, $opts);
        $opts = [];
        foreach($_GET as $k=>$v){
            $opts[] = $k;
        }
//        $battle = $this->battle->getBattle($game, null, $arg, $opts);


        $cs->setDb('mydatabase');
        if (method_exists($battle, 'terrainInit')) {
            try{
                $terrainName = "terrain-$game.$arg";
                $terrainDoc = $cs->get($terrainName);
            }catch(\GuzzleHttp\Exception\BadResponseException $e){}
            if(!$terrainDoc){
                try{
                    $terrainName = "terrain-$game";
                    $terrainDoc = $cs->get($terrainName);
                }catch(\GuzzleHttp\Exception\BadResponseException $e){var_dump($e->getMessage());}
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
        $doc->wargame->gameRules->phaseClicks[] = $click + 1;
        /* should probably get rid of this old code for genTerrain */
        if (isset($doc->wargame->genTerrain)) {
            try {
                $ter = $cs->get($doc->wargame->terrainName);
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            };
            if (!$ter) {
                $data = array("_id" => $doc->wargame->terrainName, "docType" => "terrain", "terrain" => $doc->wargame->terrain);
                $cs->post($data);
            } else {
                $data = array("_id" => $doc->wargame->terrainName, "docType" => "terrain", "terrain" => $doc->wargame->terrain);
                /* totally throw the old one away */

                $cs>delete($doc->wargame->terrainName, $ter->_rev);
                $cs->post($data);

            }
            unset($doc->wargame->terrain);
            $doc->wargame->genTerrain = false;

        }

        $doc->className = get_class($battle);
        $doc->chats = array();
        $doc->gameName = $game;

        $doc = $cs->put($doc->_id, $doc);
        return redirect("wargame/play-as/$game");

    }


    function getPlayAs(CouchService $cs, Request $req, $game = false)
    {
        $user = Auth::user()['name'];
        $wargame = urldecode($req->session()->get("wargame"));
        if (!$wargame && $game) {
            $wargame = $game;
        }
//        $wargame = "MainWargame";
        $cs->setDb('mydatabase');
        $doc = $cs->get(urldecode($wargame));
        if (!$doc || $doc->createUser != $user) {
            redirect("wargame/play");
        }
        $game = $doc->gameName;
        $arg = $doc->wargame->arg;
        echo "<!doctype html><html>";
        $className = isset($doc->className)? $doc->className : '';
        $viewPath = preg_replace("/\\\\/", ".", $className);
        $viewPath = preg_replace("/\.[^.]*$/","", $viewPath).".playAs";
        return "<!doctype html><html>".view("wargame::".$viewPath, compact("game", "user", "wargame", $doc->wargame, "arg"))."</html>";
//        \Wargame\Battle::playAs($game,$wargame, $arg);
        echo "</html>";
        return;
        $this->load->view("wargame/wargamePlayAs", compact("game", "user", "wargame", $doc->wargame, "arg"));
    }


    public function getEnterHotseat(Request $req, CouchService $cs, $newWargame = false)
    {
        if (!$newWargame) {
            redirect("wargame/play");
        }
        $wargame = $req->session()->get("wargame", $cs);
//        $this->load->model("wargame/wargame_model");
//        $ret = $this->wargame_model->enterHotseat($newWargame);
        $ret = $this->enterHotseat($newWargame, $cs);
        if ($ret) {
            return redirect("wargame/change-wargame/$newWargame");
        } else {
            return redirect("wargame/play");
        }
    }

    public function getEnterMulti(CouchService $cs, AdminService $ad, $wargame = false, $playerOne = "", $playerTwo = "", $visibility="", $playerThree = "", $playerFour = "")
    {
        $user = Auth::user()['name'];
        if (!$wargame) {
            redirect("wargame/play");

        }
//        $this->load->model('wargame/wargame_model');
        $cs->setDb('mydatabase');
        $doc = $cs->get($wargame);
        if(!$visibility){
            if(!empty($doc->visibility)){
                $visibility = $doc->visibility;
            }
        }
        if(!$visibility){
            $visibility = "public";
        }
        if (!$doc || $doc->createUser != $user) {
            redirect("wargame/play");
        }

        $scenario = $doc->wargame->scenario;
        if(isset($scenario->maxPlayers)){
            $maxPlayers = $scenario->maxPlayers;
        }else{
            $maxPlayers = 2;
        }
        if ($playerOne == "") {
            $users = $ad->getUsersByUsername();
            foreach ($users as $k => $val) {
                if ($val['name'] == $user) {
                    unset($users[$k]);
                    continue;
                }
                $users[$k] = $val;
            }

            $doc = $cs->get(urldecode($wargame));
            if (!$doc || $doc->createUser != $user) {
                redirect("wargame/play");
            }
            $game = $doc->gameName;

            $path = url("wargame/enter-multi");
            $me = $user;
            $others = $users;
            $pOne = isset($scenario->playerOne) ? $scenario->playerOne : '';
            $pTwo = isset($scenario->playerTwo) ? $scenario->playerTwo : '';

            $players = ["neutral", $pOne, $pTwo];
            $arg = $doc->wargame->arg;
            $className = isset($doc->className)? $doc->className : '';
            $viewPath = preg_replace("/\\\\/", ".", $className);
            $viewPath = preg_replace("/\.[^.]*$/","", $viewPath).".playMulti";
//            Battle::playMulti($game, $wargame, $arg);
//            $this->parser->parse("wargame/wargameMulti", compact("maxPlayers","players","visibility", "game", "users", "wargame", "me", "path", "others", "arg"));
            return view('layouts/playMulti', compact("viewPath", "maxPlayers","players","visibility", "game", "users", "wargame", "me", "path", "others", "arg"));
                return 'jfj';
        }

//        $wargame = $this->session->userdata("wargame");
//        $this->load->model("wargame/wargame_model");
        if ($playerTwo == "") {
            $playerTwo = $user;
        }
//        $this->wargame_model->enterMulti($wargame, $playerOne, $playerTwo, $visibility, $playerThree, $playerFour);
        return redirect("wargame/change-wargame/$wargame");
    }


    public function enterHotseat($wargame, CouchService $cs )
    {
        $cs->setDb('mydatabase');
        $user = Auth::user()['name'];
        try {
            $doc = $cs->get($wargame);
        } catch (Exception $e) {
            return false;
        }
        if (!$doc || $user != $doc->createUser) {
            return false;
        }
        $doc->playerStatus = "hot seat";
        $doc->wargame->players[1] = $doc->wargame->players[2] = $user;
//        foreach($doc->wargame->players as $k => $v){
//            if($v != $user){
//                $doc->wargame->players[$k] = "";
//            }
//        }
//        $doc->wargame->players[1] = $user;
        $doc->wargame->gameRules->turnChange = true;
        $cs->put($doc->_id, $doc);
        return true;
    }


    function getChangeWargame(Request $req, CouchService $cs, $newWargame = false)
    {
        $wargame = $req->session()->get("wargame");

        if ($newWargame == false) {
            $newWargame = $wargame;
        }
        $cs->setDb('mydatabase');
        if ($cs->get($newWargame)) {

            $req->session()->put("wargame", $newWargame);
        }
        return redirect("/wargame/play");
    }


    public function getFetch(Request $req, CouchService $cs, $last_seq = '')
    {
        $user = Auth::user()['name'];

        if (!$user) {
            header("Content-Type: application/json");
            echo json_encode(['forward'=> site_url('/users/login')]);
            return;
        }
        $wargame = urldecode($req->session()->get("wargame"));


        header("Content-Type: application/json");
//        $this->load->model("wargame/wargame_model");
        $chatsIndex = Input::get('chatsIndex');
        /* @var Wargame_Model $this ->wargame_model */
        $ret = $this->grabChanges($cs,$req, $wargame, $last_seq, $chatsIndex, $user);
        return $ret;
    }




    public function grabChanges($cs, $req,$wargame, $last_seq = 0, $chatsIndex = 0, $user = 'observer')
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
        $cs->setDb('mydatabase');
        if (!$time) {
            do {
                $retry = false;
                try {
                    if ($last_seq) {
                        $seq = $cs->get("/_changes?since=$last_seq&feed=longpoll&filter=newFilter/namefind&name=$wargame");
                    } else {
                        $seq = $cs->get("/_changes");
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
            $doc = $cs->get($wargame . "?revs_info=true");
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
        $doc = $cs->get($wargame . $revision);
        if ($timeBranch) {
            $doc->_rev = $currentRev;
            $doc->wargame->gameRules->flashMessages[] = "Time Travel to click $time";
            $cs->put($doc->_id, $doc);
            $last_seq = 0;
        }

        if($timeFork){
            $doc->wargame->gameRules->flashMessages[] = "Time Travel to click $time";
            $doc->forkedFrom = $doc->_id;
            unset($doc->_id);
            unset($doc->_rev);
            $doc->name .= "Clone $time";
            $ret = $cs->post($doc);
            $last_seq = 0;

            if (is_object($ret) === true) {
                $wargame = $ret->body->id;
                $req->session()->put("wargame", $wargame);
            }
            $doc = $cs->get($wargame);

        }

        return \Wargame\Battle::transformChanges($doc, $last_seq, $user);
    }

    public function postPoke(Request $req, CouchService $cs)
    {
        $user = Auth::user()['name'];

        $wargame = urldecode($req->session()->get("wargame"));

        $x = (int)Input::get('x', FALSE);
        $y = (int)Input::get('y', FALSE);
        $event = (int)Input::get('event', FALSE);
        $id = Input::get('id', FALSE);

//        $this->load->model("wargame/wargame_model");
        /*  @var  Wargame_model */
        $cs->setDb('mydatabase');
        $doc = $cs->get(urldecode($wargame));
        $ter = false;
        if (!empty($doc->wargame->terrainName)) {
            try {
                $ter = $cs->get($doc->wargame->terrainName);
            }catch(\GuzzleHttp\Exception\BadResponseException $e){var_dump($e->getMessage());}
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
            $battle = Battle::getBattle($game, $doc->wargame, $doc->wargame->arg);
            $doSave = $battle->poke($event, $id, $x, $y, $user, $click);
            $success = false;
            if ($doSave) {
                $doc->wargame = $battle->save();

                $cs->put($doc->_id, $doc);
                $success = true;

            }
            if ($doSave === 0) {
                $success = true;
            }
        } catch (Exception $e) {
            $emsg = $e->getMessage() . " \nFile: " . $e->getFile() . " \nLine: " . $e->getLine() . " \nCode: " . $e->getCode();
            $success = false;
        }
        if (!$success) {
            header("HTTP/1.1 404 Not Found");
        }
        return compact('success', "emsg");
    }


}