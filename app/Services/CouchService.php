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


class CouchService extends \GuzzleHttp\Client
{
    public $dbName = "games";
    public $db = "";

    public function setDb($newDb){
        $prevDb = $this->dbName;
        $this->dbName = $newDb;
        $this->db = \Config::get('couch')[$newDb];
        return $prevDb;
    }

    public function get($id){
        $resp = $this->request('GET', $this->db . "/$id");
        return json_decode((string)$resp->getBody());
    }

    public function put($id, $doc){
        $resp = $this->request('PUT', $this->db."/$id",['json'=>$doc]);
        return json_decode((string)$resp->getBody());
    }

    public function post($doc){
        $resp = $this->request('POST', $this->db."/",['json'=>$doc]);
        return json_decode((string)$resp->getBody());
    }
    public function delete($id, $rev){
        $resp = $this->request('DELETE', $this->db."/$id?rev=$rev");
        return json_decode((string)$resp->getBody());
    }
    public function createDb($name){
        $resp = $this->request('POST', "$name");
    }
}