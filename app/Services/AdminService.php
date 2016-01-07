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
        $prevDb = $this->cs->setDb($this->couch['userName']);
        $logins = $this->cs->get("userLogins");
        $this->cs->setDb($prevDb);
        return $logins;
    }

    public function getUsersByUsername(){
        $prevDb = $this->cs->setDb($this->couch['userName']);
        $seq = $this->cs->get("/_design/newFilter/_view/userByUsername");
        $this->cs->setDb($prevDb);
        return $seq->rows;

    }

    public function getAllGames(){

        $prevDb = $this->cs->setDb($this->couch['dbName']);

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
        $prevDb = $this->cs->setDb($this->couch['userName']);

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