<?php
/**
 * Created by PhpStorm.
 * User: david_rodal
 * Date: 1/7/16
 * Time: 2:49 PM
 */

namespace App\Http\Controllers;
use App\Services\AdminService;
use Illuminate\Http\Request;
use App\Services\CouchService;
use Auth;
use DateTime;
use Wargame\Battle;
use App\Services\WargameService;
use App\User;
use App\Services\AnalyticsService;
use App\Jobs\RunMakeGame;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;


class WargameController extends Controller
{
    public $ws;

    public function __construct(WargameService  $ws)
    {
        $this->ws = $ws;
    }

    /**
     * Handle user login events.
     */
    public function handleUserLogin($event) {
        $user = Auth::user()['name'];
        $this->ws->doChatPoke($user, "Has logged in");
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout($event) {
        $user = Auth::user()['name'];
        $this->ws->doChatPoke($user, "Has logged out");
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'App\Http\Controllers\WargameController@handleUserLogin'
        );

        $events->listen(
            'Illuminate\Auth\Events\Logout',
            'App\Http\Controllers\WargameController@handleUserLogout'
        );
    }

    public function getIndex(){
    }

    public function getPlay(Request $req, WargameService $ws, $wargame = false){

//        $wargame = $req->session()->get('wargame');


        Log::debug("debugme ");
        if (!$wargame) {
            $ret = $ws->lobbyView($wargame);

            return view("wargame/lobby", $ret );

        }
        $ret = $ws->gameView($wargame);
        if($ret === false){
            return redirect("/wargame/play");
        }
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
//        $req->session()->put('wargame', $id);
        return redirect('wargame/play');
    }

    public function getTestAnalytics(AnalyticsService $as){
        $displayData = $gamesAvail = $as->getVictories();

        return view("wargame/wargame-analytics", compact("displayData"));

    }

    public function getTestScenarioAnalytics(AnalyticsService $as, $game = false){
        $displayData = $as->getScenarioVictories($game);

        return view("wargame/wargame-scenario-analytics", compact("displayData"));
    }

    function getCloneScenario(AdminService $ad, WargameService $ws, $dir = false, $genre = false, $game = false, $theScenario = false){

//        $scenario = new \stdClass();
//        $scenario->description = 'jj ';
//        dd($ad->cloneScenario($dir,$genre,$game,uniqid(), $scenario));

        $cloneScenario = $ad->getScenarioByName($dir, $genre, $game, $theScenario);
        if($cloneScenario !== false){
            if(!isset($cloneScenario->origTerrainName)){
                $cloneScenario->origTerrainName = $ws->getTerrainName($game, $theScenario);
            }
            $ad->cloneScenario($dir, $genre, $game,uniqid(), $cloneScenario);
        }
        return redirect("/wargame/unattached-game/$dir/$genre/$game");
    }

    function getScenarioDelete(Request $req, AdminService $ad, $id){
        $cloneScenario = $ad->DeleteScenarioById($id);
        $req->headers->get('referer');
        return redirect($req->headers->get('referer'));

    }

    function getUnattachedGame(WargameService $ws, AdminService $ad, CouchService $cs, Request $req, $dir = false, $genre = false, $game = false, $theScenario = false)
    {

        $plainGenre = rawurldecode($genre);
        $cs->setDb("games");
        $seq = $cs->get("_design/newFilter/_view/getLobbies");
        $games = array();
        $theGame = false;
        $editor = Auth::user()->is_editor;
        $siteUrl = url("wargame/unattached-game/");

        $gamesAvail = $ad->getAvailGames($dir, $genre, $game);

        $backgroundImage = "Egyptian_Pharaoh_in_a_War-Chariot,_Warrior,_and_Horses._(1884)_-_TIMEA.jpg";
        $backgroundAttr = 'By Unknown author [<a target="blank" href="http://creativecommons.org/licenses/by-sa/2.5">CC BY-SA 2.5</a>], <a target="blank" href="http://commons.wikimedia.org/wiki/File%3AEgyptian_Pharaoh_in_a_War-Chariot%2C_Warrior%2C_and_Horses._(1884)_-_TIMEA.jpg">via Wikimedia Commons</a>';
        if($genre){
            if(preg_match("/18%27th/", urlencode($genre))){
                $backgroundImage = "18th_century_gun.jpg";
                $backgroundAttr = 'By MKFI (Own work) [Public domain], <a href="http://commons.wikimedia.org/wiki/File%3ASwedish_18th_century_6_pound_cannon_front.JPG">via Wikimedia Commons</a>';
            }
            if(preg_match("/Medieval/", urlencode($genre))){
                $backgroundImage = "Helmeted_Medieval_Knight_or_Soldier.jpg";
                $backgroundAttr = '            By Paul Mercuri [Public domain], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3AHelmeted_Medieval_Knight_or_Soldier_(1).JPG">via Wikimedia Commons</a>';
            }
            if(preg_match("/Americas/", $genre)){
                $backgroundImage = "Yorktown80.jpg";
                $backgroundAttr = 'John Trumbull [Public domain], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3AYorktown80.JPG">via Wikimedia Commons</a>';
            }
            if(preg_match("/Great Northern/", $genre)){
                $backgroundImage = "Battle_of_Poltava_1709.png";
                $backgroundAttr = 'By Louis Caravaqe [Public domain], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3ABattle_of_Poltava_1709.PNG">via Wikimedia Commons</a>';
            }
            if(preg_match("/Napoleonic/", $genre)){
                $backgroundImage = "Napoleon.jpg";
                $backgroundAttr = 'Jacques-Louis David [Public domain], <a href="https://commons.wikimedia.org/wiki/File%3AJacques-Louis_David_-_Napoleon_at_the_St._Bernard_Pass_-_WGA06083.jpg">via Wikimedia Commons</a>';
            }
            if(preg_match("/Spanish Succession/", $genre)){
                $backgroundImage = "Vendome-and-PhilipV.jpg";
                $backgroundAttr = 'Jean Alaux [Public domain], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3AVendome-and-PhilipV.jpg">via Wikimedia Commons</a>';
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
                $terrain = $ws->getTerrain($terrainName);
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
                    $terrain = $ws->getTerrain($terrainName);
                }catch(\GuzzleHttp\Exception\BadResponseException $e){}
                if(!$terrain){
                    $terrain = $ws->getTerrain("terrain-".$game);
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
//                $terrainName = "terrain-".$game;
                if(!isset($customScenario->value->origTerrainName)){
                    $terrainName = "terrain-".$game;
                }else{
                    $terrainName = $customScenario->value->origTerrainName;
                }
                $theScenario = $customScenario->scenario;
//                if($theScenario){
//                    $terrainName .= ".$theScenario";
//                }
                try {
                    $terrain = $cs->get($terrainName);
                }catch(\GuzzleHttp\Exception\BadResponseException $e){}
                if(!$terrain){
                    $terrain = $cs->get("terrain-".$game);
                }
                $thisScenario = $customScenario->value;
                $thisScenario->sName = $theScenario;
                $thisScenario->id = $customScenario->id;
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
            $feed = false;
//            try{
//                @$feed = file_get_contents("http://davidrodal.com/pubs/category/$gameFeed/feed");
//            }catch(Exception $e){}
            $theGameMeta = (array)$theGame->value;
            $theGameMeta['options'] = isset($theGameMeta['options'])? $theGameMeta['options'] : [];
            $fileName = $theGame->value->fileName;
            $fileName = preg_replace("/.php$/","", $fileName);
            $theGameMeta['playerInfo'] = $fileName::getPlayerData($thisScenario);
            WargameService::$viewBase = "Wargame";
            $className = $theGame->value->fileName;
            $className = preg_replace("/^\\\\/","", $className);
            $theGameMeta['curPath'] = WargameService::viewBase($className);
            $theGameMeta['corePath'] = $thisScenario->corePath ?? null;
            unset($theGameMeta->scenarios);
            $feed = false;
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
            $className = $fileName;
            $className = preg_replace("/^./","", $className);
            $viewPath = WargameService::viewBase($className).".playMulti";
            $scenario = $thisScenario;
            return view("wargame/wargame-unattached-game", compact("scenario", "viewPath", "game","dir","theScenarios", "editor", "backgroundImage", "backgroundAttr","bigMapUrl", "mapUrl", "theScenario", "plainGenre", "theGame", "games", "siteUrl","theGameMeta"));
        } else {
            foreach ($gamesAvail as $gameAvail) {
                if($gameAvail->game) {
                    $terrainName = "terrain-" . $gameAvail->game;
                    $terrain = "";
                    try {
                        $terrain = $ws->getTerrain($terrainName);
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
            $className = isset($doc->className)? $doc->className : '';

            $viewPath = WargameService::viewBase($className).".playMulti";

            if(count($games) > 0 && !empty($games[0]->game)){
                return view("wargame/wargame-unattached-games", compact("theScenarios", "theGameMeta", "editor", "backgroundAttr", "backgroundImage","theScenario", "plainGenre", "theGame", "games", "siteUrl"));

            }

            return view("wargame/wargame-unattached", compact("viewPath","theScenarios", "theGameMeta", "editor", "backgroundAttr", "backgroundImage","theScenario", "plainGenre", "theGame", "games", "siteUrl"));
        }
    }

    public function getScenarioEdit($id){
        $backgroundImage = '18th_century_gun.jpg';
        $backgroundAttr = 'By MKFI (Own work) [Public domain], <a href="http://commons.wikimedia.org/wiki/File%3ASwedish_18th_century_6_pound_cannon_front.JPG">via Wikimedia Commons</a>';

        return view('scenario/edit',compact("backgroundImage", "backgroundAttr"));
    }

    public function getScenarioVueEdit($id){
        $backgroundImage = "BWP-1_Baltops_2016_0283.jpg";
        $backgroundAttr = '<a rel="nofollow" target="_blank" class="external text" href="http://konflikty.pl">Konflikty.pl</a> [Attribution or Attribution], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3ABWP-1_Baltops_2016_0283.jpg">via Wikimedia Commons</a>';

        return view('scenario/vue-edit',compact("backgroundImage", "backgroundAttr"));
    }

    public function getApplyDeploys(Request $req, CouchService $cs, WargameService $ws, $wargame, $deploy)
    {
        Log::debug("love");
        $opts = "";
        $cs->setDb('games');
        $paramObj = $cs->get($wargame);

        $class =  $paramObj->className;
        $arg = $paramObj->wargame->arg;
        $opts = $paramObj->opts;
        $outOpts = "";
        /* for checking later */

        $exitCode = Artisan::call('clicks:play', ['--byDeploy'=>true,
            'clicksId' => $deploy, 'wargame' => $wargame
        ]);

    }

        public function getListDeploys(Request $req, CouchService $cs, WargameService $ws, $wargame){

        $opts = "";
        $cs->setDb('games');
        $paramObj = $cs->get($wargame);

        $class =  $paramObj->className;
        $arg = $paramObj->wargame->arg;
        $opts = $paramObj->opts;
        $outOpts = "";
        foreach($opts as $opt){
            if($opt === "fogDeploy"){
                continue;
            }
            $outOpts .= $opt."&";
        }
        $outOpts = preg_replace("/&$/","",$outOpts);

        $class = preg_replace("/\\\\/",'\\\\\\\\', $class);

        $playerId = $paramObj->wargame->gameRules->attackingForceId;

        $cs->setDb('params');
        $query = "_design/paramEvents/_view/byDeploys?reduce=false&startkey=[\"$class\",\"$arg\",\"$outOpts\",$playerId]&endkey=[\"$class\",\"$arg\",\"$outOpts\",$playerId,\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";

        $seq = $cs->get($query);
        $ids = [];

        foreach($seq->rows as $row){
            $ids[] = $row->id;
        }

        return ["success"=>true, "files"=>$ids];

    }

    public function getMakeNewGame(Request $req, CouchService $cs, WargameService $ws, $historyFile){

        $opts = "";
        $cs->setDb('params');
        $paramObj = $cs->get($historyFile);
        $game = $paramObj->gameName;
        $wargame = "unholySpawn";

        $ret = $ws->createWargame($wargame);
        $arg = $paramObj->arg;

        if (is_object($ret) === true) {

            $opts = $paramObj->opts;
            $wargame = $ret->id;

            $cs->setDb('games');
            $doc = $cs->get(urldecode($wargame));
            $firstClick = false;
            if($paramObj->history[0]->event === 'preunitinit'){
                $firstClick = $paramObj->history[0]->dieRoll;
            }
            $ws->gameUnitInit($doc, $game, $arg, $opts, $firstClick);
            $this->enterHotSeat($wargame, $cs);
            $cs->setDb('games');
            if ($cs->get($wargame)) {
//                $req->session()->put("wargame", $wargame);
                $cs->setDb('games');
//                $job = (new RunMakeGame( $historyFile, $wargame));
//                $this->dispatch($job);
                Artisan::queue('clicks:play', [
                    'clicksId' => $historyFile, 'wargame' => $wargame
                ]);
                return redirect("/wargame/play/$wargame");
            }
            return redirect("/wargame/play");


        }
    }

    public function postCreateWargame(CouchService $cs, WargameService $ws, Request $req,$type,$game, $scenario)
    {

        $message = "";
        $wargame = \Request::input('wargame');
        $user = Auth::user()['name'];

        if ($wargame) {
            $ret = $ws->createWargame($wargame);
            if (is_object($ret) === true) {
//                $req->session()->put("wargame" , $ret->id);
//                $opts = "";
//
//                foreach($_GET as $k => $v){
//                    $opts .= "$k=$v&";
//                }
//                return redirect("/wargame/unit-init/$game/$scenario?$opts");

                $docId = $ret->id;
                $doc = $cs->get($docId);
                if ($user != $doc->createUser) {
                    return redirect("wargame/play");
                }

                $opts = [];
                foreach($_GET as $k=>$v){
                    $opts[] = $k;
                }
                $ws->gameUnitInit($doc, $game, $scenario, $opts);

                if($type === "hotseat"){
                    $ret = $this->enterHotseat($docId, $cs);
                    if ($ret) {
                        return redirect("wargame/play/$docId");
                    } else {

                        return redirect("wargame/play");
                    }
                }
                return redirect("wargame/enter-multi/$docId");
            }
            $message = "Name $wargame already used, please enter new name";
        }
        if ($this->input->post()) {
            $message = "Please in put a name (need not be unique)";
        }
        $this->load->view("wargame/wargameCreate", compact("message", "game","scenario"));
    }


    function getLeaveGame(Request $req)
    {
        $user = Auth::user()['name'];
        $this->ws->doChatPoke($user, "Has entered the lobby");
        return redirect("/wargame/play");
    }

    function getDeleteGame(CouchService $cs, WargameService $ws, $gameName){
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
            $ws->removeClicksByWrgame($gameName);
        }
        echo json_encode(["success"=>true, "emsg"=>false]);
    }

    public function getUnitInit( Request $req,CouchService $cs, WargameService $ws, $game, $arg = false)
    {
        $user = Auth::user()['name'];
//        $wargame = urldecode($req->session()->get("wargame"));
        $cs->setDb('games');
        $doc = $cs->get(urldecode($wargame));
        if ($user != $doc->createUser) {
            return redirect("wargame/play");
        }

        $opts = [];
        foreach($_GET as $k=>$v){
            $opts[] = $k;
        }

        $ws->gameUnitInit($doc, $game, $arg, $opts);
        return redirect("wargame/play-as/$game");

    }


    function getPlayAs(CouchService $cs, Request $req, $game = false, $docId)
    {
        $user = Auth::user()['name'];
//        $wargame = urldecode($req->session()->get("wargame"));
        $wargame = $docId;
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
        $className = isset($doc->className)? $doc->className : '';
        $scenario = $doc->wargame->scenario;
        $viewPath = WargameService::viewBase($className).".playAs";
        return view("wargame::".$viewPath, compact("scenario", "game", "user", "wargame", $doc->wargame, "arg"))."</html>";
    }


    public function getEnterHotseat(Request $req, CouchService $cs, $newWargame = false)
    {
        if (!$newWargame) {
            redirect("wargame/play");
        }
//        $wargame = $req->session()->get("wargame", $cs);
//        $this->load->model("wargame/wargame_model");
//        $ret = $this->wargame_model->enterHotseat($newWargame);
        $ret = $this->enterHotseat($newWargame, $cs);
        if ($ret) {
            return redirect("wargame/play/$newWargame");
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
            return view('layouts/playMulti', compact("friends","deployName", "forceName", "viewPath", "maxPlayers","players","visibility", "game", "users", "wargame", "me", "path", "others", "arg", "scenario"));
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

    public function
    enterHotseat($wargame, CouchService $cs )
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
        $doc->wargame->players = array("", $user, $user);
        $doc->wargame->gameRules->turnChange = true;
        $cs->put($doc->_id, $doc);
        event(new \App\Events\Analytics\RecordGameEvent(['docId'=>$doc->_id, 'type'=>'hotseat-entered', 'className'=> $doc->className, 'scenario'=>$doc->wargame->scenario, 'arg'=>$doc->wargame->arg, 'time'=>time()]));

        return true;
    }


    function getWargameClicks(CouchService $cs, $wargame){
        $prevDb = $cs->setDb('clicks');
        $seq = $cs->get("_design/clickEvents/_view/byWargame?reduce=false&startkey=[\"$wargame\", 0]&endkey=[\"$wargame\", 100000]");
        echo "getting to it";
        foreach($seq->rows as $id => $val){
            echo "ID $id ";
            var_dump($val);
        }
    }

    function getRemoveClicks(WargameService $ws, $wargame){
        $ws->removeClicksByWrgame($wargame);
    }

    function getChangeWargame(Request $req, CouchService $cs, $newWargame = false)
    {
//        $wargame = $req->session()->get("wargame");
//
//        if ($newWargame == false) {
//            $newWargame = $wargame;
//        }
//        $cs->setDb('games');
//        if ($cs->get($newWargame)) {
//
//            $req->session()->put("wargame", $newWargame);
//        }
        $user = Auth::user()['name'];
        $this->ws->doChatPoke($user, "Has left the lobby and is in a game");
        return redirect("/wargame/play/$newWargame");
    }


    public function getFetch(Request $req, CouchService $cs, WargameService $ws, $wargame, $last_seq = '')
    {
        Log::debug("fetching");
        $user = Auth::user()['name'];

        if (!$user) {
            echo json_encode(['forward'=> site_url('/users/login')]);
            return;
        }

//        $wargame = urldecode($req->session()->get("wargame"));
        $ret = $ws->grabChanges($req, $wargame, $last_seq, $user);
        Log::debug("fetched");
        return $ret;
    }

    public function chatPoke( Request $req,WargameService $ws, CouchService $cs)
    {
        $user = Auth::user()['name'];
        $cs->setDb('games');
        $chat = $req->input('chat', FALSE);
        $ws->doChatPoke($user, $chat);
        return "User $user, chat $chat";
    }



    public function postPoke( Request $req,WargameService $ws, CouchService $cs)
    {
        Log::debug("Top of postPoke");
        $user = Auth::user()['name'];

        $cs->setDb('games');
        $wargame = (string)\Request::input('wargame', FALSE);

        $x = (int)$req->input('x', FALSE);
        $y = (int)$req->input('y', FALSE);
        $event = (int)$req->input('event', FALSE);
        $id = $req->input('id', FALSE);
        $gameType = $req->input('type', FALSE);
        $commands = $req->input('commands', []);
        $builds = $req->input('builds', []);

        if($gameType && $gameType == 'area-game'){
            $ret = $ws->doAreaPoke($wargame, $event, $id, $commands, $builds, $x, $y, $user, $dieRoll = false);
            if (!$ret['success']) {
                header("HTTP/1.1 404 Not Found");
            }
            return $ret;
        }
        Log::debug("Before do poke");
        $ret = $ws->doPoke($wargame, $event, $id, $x, $y, $user);
        Log::debug("after do poke");
        if (!$ret['success']) {
            header("HTTP/1.1 404 Not Found");
        }
        Log::debug("Returning from poke");
        return $ret;
    }

    public function terrainInit(CouchService $cs, WargameService $ws,  $game = "MartianCivilWar", $arg = false, $terrainDocId = false)
    {
        if(Auth::user()){
            $user = Auth::user()['name'];
        }else{
            $user = "Markarian";
        }


        $battle = Battle::battleFromName($game, $arg);
        if($battle === false){
            $ret = new \stdClass();
            $ret->ok = false;
            return response()->json($ret);
        }

        if(method_exists($battle, 'publishAreaMap')){
            $cs->setDb("rest");
            $terrainDoc = $cs->get($terrainDocId);
            if($terrainDoc->docType !== "areaMapData"){
                echo "No MapData ";
                return;
            }
            $battle->publishAreaMap($terrainDoc);
        } else {
            if (method_exists($battle, 'terrainGen')) {
                $cs->setDb("rest");
                $terrainDoc = $cs->get($terrainDocId);
                $mapId = $terrainDoc->hexStr->map;
                $mapDoc = $cs->get($mapId);
                $battle->terrainGen($mapDoc, $terrainDoc);
            } else {
                echo "No TerrainGen ";
                return;
            }
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

    public function getMakePublic(WargameService $ws, $docId){
        $ret = $ws->makePublic($docId);
        echo json_encode(["success"=>$ret, "emsg"=>false]);
    }

    public function getMakePrivate(WargameService $ws, $docId){
        $ret = $ws->makePrivate($docId);
        echo json_encode(["success"=>$ret, "emsg"=>false]);
    }

    public function getCustomScenario(AdminService $ad, $id)
    {
        $user = Auth::user()['name'];

        if (!$user) {
            echo json_encode(['forward'=> site_url('/users/login')]);
            return;
        }

        $cloneScenario = $ad->getScenarioById($id);

        $gA = get_object_vars($cloneScenario->games);
        $game = array_keys($gA)[0];

        $gS = get_object_vars($cloneScenario->games->$game->scenarios);
        $scenario = array_keys($gS)[0];
        echo json_encode($cloneScenario->games->$game->scenarios->$scenario);
    }

    public function putCustomScenario(AdminService $ad,  Request $request, $id, $scenario )
    {

        $postData = $request->all();
        $user = Auth::user()['name'];

        if (!$user) {
            echo json_encode(['forward'=> site_url('/users/login')]);
            return;
        }

        $ad->putScenarioById($id, $scenario, $postData);

    }
}