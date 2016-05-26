<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 5/26/16
 * Time: 6:20 PM
 */

namespace app\Services;


class AnalyticsService
{
    public $cs;
    public function __construct(CouchService $cs)
    {
        $this->cs = $cs;
        $couch = \Config::get('couch');
        $this->couch = $couch;
    }

    public function getVictories(){
        
        $query = "/_design/gameEvents/_view/byEventType?group=true&group_level=3&startkey=[\"game-victory\"]&endkey=[\"game-victory%20\"]";
        $prevDb = $this->cs->setDb('analytics');

        $seq = $this->cs->get($query);
        $rows = $seq->rows;
        $display = [];
        foreach($rows as $row){
            $name = $row->key[1];
            $name = preg_replace("/.*\\\\/", "", $name);

            $playerId = $row->key[2];
            if(empty($display[$name])){
                $display[$name] = [0,0,0];
            }
            $display[$name][$playerId] = $row->value;
        }
//        foreach($display as $gameName => $gameData){
//            $dName = preg_replace("/.*\\\\/", "", $gameName);
//            echo "$dName ".$gameData[0]." ".$gameData[1]." ". $gameData[2]."<br>";
//        }
        return $display;
    }
}