<?php
/**
 * Copyright 2016 David Rodal
 * User: David Markarian Rodal
 * Date: 1/5/16
 * Time: 8:13 PM
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Http\Controllers;

use App\Services\CouchService;
use App\Http\Requests;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /* @var CouchService */
    protected $cs;
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function anyIndex()
    {
        return view('admin.home');
    }

    function getGames(CouchService $cs)
    {
        $this->cs = $cs;
        $game = [];
        $games = $this->availGames(true);
        return view('admin.games', ['games' => $games]);
    }

    function getAllgames(CouchService $cs, Request $req){

        $user = $req->user()['name'];


//            $users = $this->couchsag->get('/_design/newFilter/_view/userByEmail');
//            $userids = $this->couchsag->get('/_design/newFilter/_view/userById');

//            var_dump($poll);
//            echo $this->wargame_model->getLobbyChanges(false,$poll);
        //$seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
        $prevDb = $cs->setDb('mydatabase');

        $seq = $cs->get("_design/newFilter/_view/allGames?");
        $lobbies = [];
        date_default_timezone_set("America/New_York");
        $odd = 0;

        foreach($seq->rows as $row){
            $keys = $row->key;
            $creator = array_shift($keys);
            $gameName = array_shift($keys);

            $name = array_shift($keys);
            $gameType = array_shift($keys);
            $id = array_shift($keys);
//               $key = implode($keys,"  ");
            $id = $row->id;
            $dt =  $row->value[1];
            $myTurn = "";
//            $row->value[1] = "created ".formatDateDiff($dt)." ago";

            $row->value[1] = "created a long long time ago";
            $odd ^= 1;

            $lobbies[] =  array("odd"=>$odd ? "odd":"","id"=>$id, "gameName" => $gameName, "name"=>$name, 'date'=>$row->value[1], "id"=>$id, "creator"=>$creator,"gameType"=>$gameType );
        }

        return view('admin.allGames',['lobbies'=>$lobbies]);
        return $lobbies;

        $this->parser->parse("admin/wargameLobbyView",compact("lobbies","otherGames","myName"));
        return;
    }
    public function availGames( $dir = false, $genre = false, $game = false){


        $reduceArgs = "group=true&group_level=2";
        if ($dir !== false) {
            $reduceArgs = "group=true&group_level=2&startkey=[\"$dir\"]&endkey=[\"$dir\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
            if ($dir === true) {
                $reduceArgs = "reduce=false";
            }

            if ($genre !== false) {
                $reduceArgs = "reduce=false&startkey=[\"$dir\",\"$genre\"]&endkey=[\"$dir\",\"$genre\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
                if ($game !== false) {
                    $reduceArgs = "reduce=false&startkey=[\"$dir\",\"$genre\",\"$game\"]&endkey=[\"$dir\",\"$genre\",\"$game\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
                }
            }
        }
        $prevDb = $this->cs->setDb('users');

        $seq = $this->cs->get("/_design/newFilter/_view/gnuGetAvailGames?$reduceArgs");
        $this->cs->setDb($prevDb);
        $rows = $seq->rows;
        $games = [];

        foreach ($rows as $row) {
            if ($dir === true) {
                $game = $row->value;
                $game->key = $row->key;
            } else {
                $game = new stdClass();
                $game->dir = $row->key[0];
                $game->genre = $row->key[1];
                $game->game = $row->key[2];
                $game->value = $row->value;
            }
            $games[] = $game;
        }

        return $games;
    }
}