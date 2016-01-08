<?php
/**
 * Created by PhpStorm.
 * User: david_rodal
 * Date: 1/7/16
 * Time: 2:49 PM
 */

namespace app\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\CouchService;
use Auth;


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
                $dt = new \DateTime($row->value[1]);
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
                $dt = new \DateTime($row->value[1]);
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

}