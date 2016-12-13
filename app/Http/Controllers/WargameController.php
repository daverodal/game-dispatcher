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
use DateTime;
use Wargame\Battle;
use App\Services\WargameService;
use App\User;
use App\Services\AnalyticsService;


class WargameController extends Controller
{

    public function getIndex(){
    }

    public function getPlay(Request $req, WargameService $ws){

        $wargame = $req->session()->get('wargame');


        if (!$wargame) {
            $ret = $ws->lobbyView($wargame);

            return view("wargame/lobby", $ret );

        }
        $ret = $ws->gameView($wargame);
        $className = $ret['className'];
        $viewPath = WargameService::viewBase($className).".view";
        if(view()->exists("wargame::$viewPath")){
            return view("wargame::$viewPath", $ret);
        }

        list($viewPath, $viewRet) = WargameService::viewParent($className);
        $viewPath .= ".view-family";
        $ret = array_merge($ret, $viewRet);

        return view("wargame::$viewPath", $ret);
    }



    public function anyFetchLobby(Request $req,WargameService $ws, CouchService $cs, $last_seq = '')
    {
        return $ws->fetchLobby($last_seq);
    }


    public function getSetplay(Request $req, $id){
        $req->session()->put('wargame', $id);
        return redirect('wargame/play');
    }

    public function getTestAnalytics(AnalyticsService $as){
        $displayData = $gamesAvail = $as->getVictories();

        return view("wargame/wargame-analytics", compact("displayData"));

    }

    function getCloneScenario(AdminService $ad,$dir = false, $genre = false, $game = false, $theScenario = false){
        $cloneScenario = $ad->getScenarioByName($dir, $genre, $game, $theScenario);
        if($cloneScenario !== false){
            dd($ad->cloneScenario('Mollwitz','Americas','FreemansFarm1777',uniqid(), $cloneScenario));
        }

    }

    function getDeleteScenario(AdminService $ad){
        $cloneScenario = $ad->deleteScenarioByName('Mollwitz','Americas','FreemansFarm1777','576ffdd3408fc');
    }

    function getUnattachedGame(AdminService $ad, CouchService $cs, Request $req, $dir = false, $genre = false, $game = false, $theScenario = false)
    {

        $gamesAvail = $ad->getAvailGames($dir, $genre, $game);
        $plainGenre = rawurldecode($genre);
        $cs->setDb("games");
        $seq = $cs->get("_design/newFilter/_view/getLobbies");
        $games = array();
        $theGame = false;
        $editor = Auth::user()->is_editor;
        $siteUrl = url("wargame/unattached-game/");


        $backgroundImage = "Egyptian_Pharaoh_in_a_War-Chariot,_Warrior,_and_Horses._(1884)_-_TIMEA.jpg";
        $backgroundAttr = 'By Unknown author [<a href="http://creativecommons.org/licenses/by-sa/2.5">CC BY-SA 2.5</a>], <a href="http://commons.wikimedia.org/wiki/File%3AEgyptian_Pharaoh_in_a_War-Chariot%2C_Warrior%2C_and_Horses._(1884)_-_TIMEA.jpg">via Wikimedia Commons</a>';
        if($genre){
            if(preg_match("/18%27th/", urlencode($genre))){
                $backgroundImage = "18th_century_gun.jpg";
                $backgroundAttr = 'By MKFI (Own work) [Public domain], <a href="http://commons.wikimedia.org/wiki/File%3ASwedish_18th_century_6_pound_cannon_front.JPG">via Wikimedia Commons</a>';
            }

            if(preg_match("/Americas/", $genre)){
                $backgroundImage = "Yorktown80.jpg";
                $backgroundAttr = 'John Trumbull [Public domain], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3AYorktown80.JPG">via Wikimedia Commons</a>';
            }
            if(preg_match("/Great Northern/", $genre)){
                $backgroundImage = "Battle_of_Poltava_1709.png";
                $backgroundAttr = 'John Trumbull [Public domain], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3AYorktown80.JPG">via Wikimedia Commons</a>';
            }
            $backgroundAttr = 'By Louis Caravaqe [Public domain], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3ABattle_of_Poltava_1709.PNG">via Wikimedia Commons</a>';
            if(preg_match("/Napoleonic/", $genre)){
                $backgroundImage = "Napoleon.jpg";
                $backgroundAttr = 'Jacques-Louis David [Public domain], <a href="https://commons.wikimedia.org/wiki/File%3AJacques-Louis_David_-_Napoleon_at_the_St._Bernard_Pass_-_WGA06083.jpg">via Wikimedia Commons</a>';
            }

            if(preg_match("/19%27th/", urlencode($genre))) {
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

            if(preg_match("/Mid/", $genre)){
                $backgroundImage = "BWP-1_Baltops_2016_0283.jpg";
                $backgroundAttr = '<a rel="nofollow" target="_blank" class="external text" href="http://konflikty.pl">Konflikty.pl</a> [Attribution or Attribution], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3ABWP-1_Baltops_2016_0283.jpg">via Wikimedia Commons</a>';
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
                $terrain = $cs->get("terrain-".$game);
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
                    $terrain = $cs->get("terrain-".$game);
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
                    $terrain = $cs->get($terrainName);
                }catch(\GuzzleHttp\Exception\BadResponseException $e){}
                if(!$terrain){
                    $terrain = $cs->get("terrain-".$game);
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
                        if($editor) {
                            $theGameMeta['histEditLink'] = $editLink;
                        }
                    }
                    if (preg_match("/Player/", $entry->title) || preg_match("/Designer/", $entry->title)) {
                        $content = $entry->children('http://purl.org/rss/1.0/modules/content/');
                        $str = $content->encoded;

                        // http://stackoverflow.com/questions/8781911/remove-non-ascii-characters-from-string-in-php
                        $str = preg_replace('/[[:^print:]]/', '', $str); // should be aA
                        $matches = [];
                        if(preg_match("/p=(\d+)$/",$entry->guid,$matches)){
                            $editLink = "http://davidrodal.com/pubs/wp-admin/post.php?post=".$matches[1]."&action=edit";
//                            $theGame->value->playerEditLink = "<a target='blank' href='$editLink'>edit</a>";
                            if($editor){
                                $theGameMeta['playerEditLink'] = "<a target='blank' href='$editLink'>edit</a>";
                            }
                        }
                        $theGameMeta['designerNotes'] = $str;
                        $theGame->value->designerNotes = $str;

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
            $cs->setDb("games");
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

    function getDeleteGame(CouchService $cs, $gameName){
        $user = Auth::user()['name'];
        $cs->setDb('games');
        if ($gameName) {
            try {
                $doc = $cs->get($gameName);
                if ($doc->createUser == $user) {
                    if ($doc && $doc->_id && $doc->_rev) {
                        $cs->delete($doc->_id, $doc->_rev);
                    }
                }
            } catch (Exception $e) {
            }
        }
        echo json_encode(["success"=>true, "emsg"=>false]);
    }
    public function getUnitInit( Request $req,CouchService $cs, AdminService $ad, $game, $arg = false)
    {
        $user = Auth::user()['name'];
        $wargame = urldecode($req->session()->get("wargame"));
        $chat = Input::get('chat', TRUE);
        $cs->setDb('games');
        $doc = $cs->get(urldecode($wargame));
        if ($user != $doc->createUser) {
            return redirect("wargame/play");
        }


        $opts = [];
        foreach($_GET as $k=>$v){
            $opts[] = $k;
        }

//        $this->load->model('users/users_model');
        $battle = Battle::battleFromName( $game, $arg, $opts);
        $opts = [];
        foreach($_GET as $k=>$v){
            $opts[] = $k;
        }
//        $battle = $this->battle->getBattle($game, null, $arg, $opts);


        $cs->setDb('games');
        if (method_exists($battle, 'terrainInit')) {
            try{
                $terrainName = "terrain-$game.$arg";
                $terrainDoc = $cs->get($terrainName);
            }catch(\GuzzleHttp\Exception\BadResponseException $e){}
            if(empty($terrainDoc)){
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
        $cs->setDb('games');
        $doc = $cs->get(urldecode($wargame));
        if (!$doc || $doc->createUser != $user) {
            redirect("wargame/play");
        }
        $game = $doc->gameName;
        $arg = $doc->wargame->arg;
        echo "<!doctype html><html>";
        $className = isset($doc->className)? $doc->className : '';
        $viewPath = WargameService::viewBase($className).".playAs";
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

    public function getEnterMulti(CouchService $cs, AdminService $ad,WargameService $ws,  $wargame = false, $playerOne = "", $playerTwo = "", $visibility="", $playerThree = "", $playerFour = "")
    {
        $user = Auth::user()['name'];
        if (!$wargame) {
            redirect("wargame/play");

        }
//        $this->load->model('wargame/wargame_model');
        $cs->setDb('games');
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
            $getUsers = $ad->getUsersByUsername();
            $users = [];
            foreach ($getUsers as $k => $val) {
                if ($val['name'] == $user) {
                    unset($users[$k]);
                    continue;
                }
                $insert = [];
                $insert['name'] = $val['name'];
                $users[$k] = $insert;
            }

            $friends = [];
            $getFriends = Auth::user()->friends()->get();
            foreach ($getFriends as $k => $val) {
                $insert = [];
                $insert['name'] = $val['name'];
                $friends[$k] = $insert;
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
            $viewPath = WargameService::viewBase($className).".playMulti";
            $playDat = $className::getPlayerData($scenario);
            $forceName = $playDat['forceName'];
            $deployName = $playDat['deployName'];
            return view('layouts/playMulti', compact("friends","deployName", "forceName", "viewPath", "maxPlayers","players","visibility", "game", "users", "wargame", "me", "path", "others", "arg"));
        }

        if ($playerTwo == "") {
            $playerTwo = $user;
        }

        $ws->enterMulti($wargame, $playerOne, $playerTwo, $visibility, $playerThree, $playerFour);

        return redirect("wargame/change-wargame/$wargame");
    }

    public function getAddFriend($id){
        $user = User::find($id);
        Auth::user()->addFriend($user);
        return redirect('wargame/play');
    }

    public function getRemoveFriend($id)
    {
        $user = User::find($id);
        Auth::user()->removeFriend($user);
        return redirect('wargame/play');
    }

    public function enterHotseat($wargame, CouchService $cs )
    {
        $cs->setDb('games');
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
        $doc->wargame->gameRules->turnChange = true;
        $cs->put($doc->_id, $doc);
        event(new \App\Events\Analytics\RecordGameEvent(['docId'=>$doc->_id, 'type'=>'hotseat-entered', 'className'=> $doc->className, 'scenario'=>$doc->wargame->scenario, 'arg'=>$doc->wargame->arg, 'time'=>time()]));

        return true;
    }


    function getChangeWargame(Request $req, CouchService $cs, $newWargame = false)
    {
        $wargame = $req->session()->get("wargame");

        if ($newWargame == false) {
            $newWargame = $wargame;
        }
        $cs->setDb('games');
        if ($cs->get($newWargame)) {

            $req->session()->put("wargame", $newWargame);
        }
        return redirect("/wargame/play");
    }


    public function getFetch(Request $req, CouchService $cs, WargameService $ws, $last_seq = '')
    {
        $user = Auth::user()['name'];

        if (!$user) {
            echo json_encode(['forward'=> site_url('/users/login')]);
            return;
        }

        $wargame = urldecode($req->session()->get("wargame"));
        $ret = $ws->grabChanges($req, $wargame, $last_seq, $user);
        return $ret;
    }





    public function postPoke(Request $req, CouchService $cs)
    {
        $user = Auth::user()['name'];

        $cs->setDb('games');
        $wargame = urldecode($req->session()->get("wargame"));

        $x = (int)Input::get('x', FALSE);
        $y = (int)Input::get('y', FALSE);
        $event = (int)Input::get('event', FALSE);
        $id = Input::get('id', FALSE);

//        $this->load->model("wargame/wargame_model");
        /*  @var  Wargame_model */
        $cs->setDb('games');
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
            $battle = Battle::battleFromDoc($doc);

            $isGameOver = $battle->victory->gameOver;
//            $battle = Battle::getBattle($game, $doc->wargame, $doc->wargame->arg, false, $doc->className);
            $doSave = $battle->poke($event, $id, $x, $y, $user, $click);
            $gameOver = $battle->victory->gameOver;
            if(!$isGameOver && $gameOver){
                event(new \App\Events\Analytics\RecordGameEvent(['docId'=>$doc->_id, 'winner'=>$battle->victory->winner, 'type'=>'game-victory', 'className'=> $doc->className, 'scenario'=>$battle->scenario, 'arg'=>$battle->arg, 'time'=>time()]));
            }
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

    public function terrainInit(CouchService $cs, WargameService $ws,  $game = "MartianCivilWar", $arg = false, $terrainDocId = false)
    {
        $user = Auth::user()['name'];


        $battle = Battle::battleFromName($game, $arg);
        if($battle === false){
            $ret = new \stdClass();
            $ret->ok = false;
            return response()->json($ret);
        }


        if (method_exists($battle, 'terrainGen')) {
            $cs->setDb("rest");
            $terrainDoc = $cs->get($terrainDocId);
            $mapId = $terrainDoc->hexStr->map;
            $mapDoc = $cs->get($mapId);
            $battle->terrainGen($mapDoc, $terrainDoc);
        }else{
            echo "No TerrainGen ";
            return;
        }

        $mapUrl = $battle->terrain->mapUrl;
        $mapWidth = $battle->terrain->mapWidth;
        if($mapWidth && $mapWidth !== "auto"){
            $mapWidth = preg_replace("/[^\d]*(\d*)[^\d]*/","$1", $mapWidth);
            $battle->terrain->mapUrl = $ws->resizeImage($mapUrl, $mapWidth, "images");
            if(!empty($mapDoc->map->trueRows)){
                $ws->rotateImage($battle->terrain->mapUrl, "images");
            }
        }
        $battle->terrain->smallMapUrl = $ws->resizeImage($mapUrl);

//        $this->rotateImage($mapUrl);
        $battle->terrainName = false;
        $wargameDoc = $battle->save();

        $terrainName = "terrain-$game";
        $ws->saveTerrainDoc(urldecode($terrainName.".".$arg), $battle);

        if(!empty($mapDoc->map->isDefault)){
            $ws->saveTerrainDoc(urldecode($terrainName), $battle);

        }
        $ret = new \stdClass();
        $ret->ok = true;
        return response()->json($ret);
    }


}