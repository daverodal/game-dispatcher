<?php

use Illuminate\Database\Seeder;

class AreaMapsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conf = \Config::get('couch');
        var_dump($conf);
        echo "boss";
        $user = $conf['username'];
        $pass = $conf['password'];
        $host = $conf['hostname'];
        $port = $conf['port'];
        $login = $user ? "$user:$pass@" : "";
        $guz = new \App\Services\CouchService([
            // Base URI is used with relative requests
            'base_uri' => "http://$login$host:$port/",
            // You can set any number of default request options.
            'timeout'  => 1800.0,
        ]);
        $guz->setDb('rest');
        $this->couchsag = $guz;
        $this->initDoc();
    }

    public function initDoc()
    {

        $views = new stdClass();
        $views->byMap = new StdClass;
        $views->byMap->map = " function(doc) {if(doc.docType == 'areaMapData'){                                   
                         emit(doc._id, doc);
                             }        }";
        $doc = false;
        $data = array("_id" => "_design/areaMaps", "views" => $views);
        try {
            $doc = $this->couchsag->get("_design/areaMaps");
        } catch (Exception $e) {
        };
        if ($doc) {
            echo "Doc Found deleting: _design/areaMaps\n";
            $deldoc = $this->couchsag->delete($doc->_id, $doc->_rev);
            if ($deldoc->id) {
                echo "Deleted\n";
            }
        }
        try {
            $this->couchsag->put($data['_id'], $data);
        } catch (Exception $e) {
            echo "Died ".$e->getMessage();
        }
    }

}
