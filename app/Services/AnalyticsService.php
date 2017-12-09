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
        
        $query = "/_design/gameEvents/_view/byEventType?group=true&group_level=4&startkey=[\"game-victory\"]&endkey=[\"game-victory%20\"]";
        $prevDb = $this->cs->setDb('analytics');

        $seq = $this->cs->get($query);
        $rows = $seq->rows;
        $display = [];
        foreach($rows as $row){
            $name = $row->key[1];
            $className = $name;
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
        }
        return $display;
    }
}