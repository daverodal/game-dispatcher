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
    }

    public function getUsersByUsername(){
        $prevDb = $this->cs->setDb('users');
        $seq = $this->cs->get("/_design/newFilter/_view/userByUsername");
        $this->cs->setDb($prevDb);
        return $seq->rows;

    }
    public function getAllGames(){
        $couch = \Config::get('couch');

        $prevDb = $this->cs->setDb($couch['dbName']);

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
            $myTurn = "";
//            $row->value[1] = "created ".formatDateDiff($dt)." ago";

            $row->value[1] = "created a long long time ago";
            $odd ^= 1;

            $lobbies[] =  array("odd"=>$odd ? "odd":"","id"=>$id, "gameName" => $gameName, "name"=>$name, 'date'=>$row->value[1], "id"=>$id, "creator"=>$creator,"gameType"=>$gameType );
        }
        $prevDb = $this->cs->setDb($couch['userName']);

        return $lobbies;
    }

}