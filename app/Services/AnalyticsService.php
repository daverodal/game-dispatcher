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

    public function getScenarioVictories($game){

        $prevDb = $this->cs->setDb('analytics');

        $game = preg_replace("/\\\\/", "$0$0", $game);

        $query = "/_design/gameEvents/_view/byEventType?group=false&startkey=[\"game-victory\",\"$game\"]&endkey=[\"game-victory\",\"$game%20\"]&reduce=false";

        $seq = $this->cs->get($query);
        $rows = $seq->rows;
        foreach($rows as $row){
            $name = $row->key[1];
            $urlClassName = $className = $name;
            if(!class_exists($className)){
                continue;
            }
            $name = preg_replace("/.*\\\\/", "", $name);
            $scenario = $row->key[2];

            $playerId = $row->key[3];
            $compName = "$name - $scenario";

            if(empty($display[$compName])){
                $display[$compName] = [0,0,0];
            }

            $pData = $className::getPlayerData($scenario)['forceName'];
            $row->value->winningSide = $pData[$row->value->winner];
        }
        return $seq->rows;
    }

    public function getVictories(){

        $query = "/_design/gameEvents/_view/byEventType?group=true&group_level=4&startkey=[\"game-victory\"]&endkey=[\"game-victory%20\"]";
        $prevDb = $this->cs->setDb('analytics');

        $seq = $this->cs->get($query);

        $rows = $seq->rows;

        $display = [];
        foreach($rows as $row){
            $name = $row->key[1];
            $urlClassName = $className = $name;
            if(!class_exists($className)){
                continue;
            }
            $name = preg_replace("/.*\\\\/", "", $name);
            $scenario = $row->key[2];

            $playerId = $row->key[3];
            $compName = "$name - $scenario";

            if(empty($display[$compName])){
                $display[$compName] = [0,0,0];
            }


            $pData = $className::getPlayerData($scenario)['forceName'];
            $display[$compName][$playerId] = $row->value;
            $display[$compName]['playerOne'] = $pData[1];
            $display[$compName]['playerTwo'] = $pData[2];
            $display[$compName]['className'] = urlencode($urlClassName);
        }
        return $display;
    }
}