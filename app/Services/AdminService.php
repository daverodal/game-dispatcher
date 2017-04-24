<?php
/**
 * Copyright 2016 David Rodal
 * User: David Markarian Rodal
 * Date: 1/3/16
 * Time: 12:07 PM
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

namespace App\Services;

class  AdminService
{
    public $cs;
    public function __construct(CouchService $cs)
    {
        $this->cs = $cs;
        $couch = \Config::get('couch');
        $this->couch = $couch;
    }

    public function getLogins(){
        $prevDb = $this->cs->setDb('users');
        $logins = $this->cs->get("userLogins");
        $this->cs->setDb($prevDb);
        return $logins;
    }

    public function getUsersByUsername(){
        $users = \Illuminate\Foundation\Auth\User::all();
        return $users;

    }

    public function getAllBugs(){

        $prevDb = $this->cs->setDb('params');

        $seq = $this->cs->get("_design/paramEvents/_view/getBugReports?");
        $lobbies = [];
        date_default_timezone_set("America/New_York");
        $odd = 0;

        foreach($seq->rows as $row){
            $id = $row->id;
            $keys = $row->key;
            $clicks = $row->value[0];
            $msg = $row->value[1];
            $odd ^= 1;

            $lobbies[] =  array("msg"=>$msg, "odd"=>$odd ? "odd":"","id"=>$id, "clicks" => $clicks, "keys"=>$keys);
        }
        $this->cs->setDb($prevDb);

        return $lobbies;
    }

    public function getAllGames(){

        $prevDb = $this->cs->setDb('games');

        $seq = $this->cs->get("_design/newFilter/_view/allGames?");
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
            $fd = formatDateDiff($dt);
            $myTurn = "";
            $row->value[1] = "created ".formatDateDiff($dt)." ago";

//            $row->value[1] = "created a long long time ago";
            $odd ^= 1;

            $lobbies[] =  array("odd"=>$odd ? "odd":"","id"=>$id, "gameName" => $gameName, "name"=>$name, 'date'=>$row->value[1], "id"=>$id, "creator"=>$creator,"gameType"=>$gameType );
        }
        $this->cs->setDb($prevDb);

        return $lobbies;
    }


    public function getAvailGames( $dir = false, $genre = false, $game = false){


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

        $seq = $this->cs->get("/_design/newFilter/_view/getAvailGames?$reduceArgs");
        $this->cs->setDb($prevDb);
        $rows = $seq->rows;
        $games = [];

        foreach ($rows as $row) {
            if ($dir === true) {
                $game = $row->value;
                $game->key = $row->key;
            } else {
                $game = new \stdClass();
                $game->dir = $row->key[0];
                $game->genre = $row->key[1];
                $game->game = isset($row->key[2]) ? $row->key[2] : '';
                $game->value = $row->value;
            }
            $games[] = $game;
        }

        return $games;
    }

    public function getGame($gameName){

        $games = $this->getAvailGames(true);
        foreach($games as $game){
            if($gameName == $game->key[2]){
                return $game;
            }
        }
        return false;
    }

    public function getNormalScenarios($dir, $genre, $game){

     $game = $this->getAvailGames($dir, $genre, $game);
        return $game;
    }

    public function getCustomScenarios($dir = false, $genre = false, $game = false){

        $reduceArgs = "group=true&group_level=2";
        if($dir !== false){
            $reduceArgs = "group=true&group_level=2&startkey=[\"$dir\"]&endkey=[\"$dir\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
            if($dir === true){
                $reduceArgs = "reduce=false";
            }
            if($genre !== false){
                $reduceArgs = "reduce=false&startkey=[\"$dir\",\"$genre\"]&endkey=[\"$dir\",\"$genre\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
                if($game !== false){
                    $reduceArgs = "reduce=false&startkey=[\"$dir\",\"$genre\",\"$game\"]&endkey=[\"$dir\",\"$genre\",\"$game\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
                }
            }
        }
        $this->cs->setDb('users');

//        var_dump("/_design/newFilter/_view/getCustomScenarios?$reduceArgs");
        $seq = $this->cs->get("/_design/newFilter/_view/getCustomScenarios?$reduceArgs");
        $this->cs->setDb('games');
        $rows = $seq->rows;
        $games = [];

        foreach($rows as $row){
            if($dir === true){
                $game = $row->value;
                $game->key = $row->key;
            }else{
                $game = new \stdClass();
                $game->dir = $row->key[0];
                $game->genre = $row->key[1];
                $game->game = $row->key[2];
                $game->scenario = $row->key[3];
                $game->id = $row->id;
                $game->value = $row->value;
            }
            $games[] = $game;
        }
        return $games;
    }

    public function deleteGame($killGame){

        if(!$killGame){
            return false;
        }
        $prevDb = $this->cs->setDb('users');
        $doc = $this->cs->get("gamesAvail");
        if(!$doc->docType == "gamesAvail"){
            return false;
        }
        unset($doc->games->$killGame);
        $ret = $this->cs->put($doc->_id, $doc);
        $this->cs->setDb($prevDb);
    }
    public function getScenarioByName($dir, $genre, $game, $scenarioName){
        $normalScenarios = $this->getNormalScenarios($dir, $genre, $game);

        $norScen = $normalScenarios[0]->value->scenarios;
        $scenarios = $this->getCustomScenarios($dir, $genre, $game);


        foreach($norScen as $sId => $scen){
            $theScenario = $sId;

            $thisScenario = $scen;
            $thisScenario->id = $sId;
            $thisScenario->sName = $theScenario;
            if($scenarioName === $theScenario){
                return $thisScenario;
            }
        }


        foreach($scenarios as $scenario){
            $theScenario = $scenario->scenario;

            $thisScenario = $scenario->value;
            $thisScenario->id = $scenario->id;
            $thisScenario->sName = $theScenario;
            if($scenarioName === $theScenario){
                return $thisScenario;
            }
        }
        return false;

    }

    public function getScenarioById( $id){

        $this->cs->setDb('users');
        $doc = $this->cs->get($id);
        return $doc;
    }

    public function deleteScenarioByName($dir, $genre, $game, $scenarioName){
        $scenario = $this->getScenarioByName($dir, $genre, $game, $scenarioName);
        if($scenario === false){
            return;
        }
        $prevDb = $this->cs->setDb('users');
        try{
            $doc = $this->cs->get($scenario->id);
        }catch(\GuzzleHttp\Exception\BadResponseException $e){}

        $this->cs->delete($scenario->id, $doc->_rev);
        $this->cs->setDb($prevDb);
    }

    public function cloneScenario( $dir, $genre, $game, $scenarioName, $scenario){
        $games = new \stdClass();
        $games->$game = new \stdClass();
        $games->$game->path = $dir;
        $games->$game->genre = $genre;
        $games->$game->scenarios = new \stdClass();
        $scenario->sName = $scenarioName;
        $scenario->description .= " Clone";
        $games->$game->scenarios->$scenarioName = $scenario;
        $doc = new \stdClass();
        $doc->docType = "scenariosAvail";
        $doc->games = $games;
        $prevDb = $this->cs->setDb('users');
        $this->cs->post($doc);
        $this->cs->setDb($prevDb);
    }
}