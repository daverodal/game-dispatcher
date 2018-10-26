<?php

use Illuminate\Database\Seeder;
use App\Services\CouchService;

class AnalyticsDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public $couchsag;
    public function run()
    {
        $conf = \Config::get('couch');
        var_dump($conf);
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
        $guz->setDb('analytics');
        $this->couchsag = $guz;
        $this->initDoc();
    }


    public function initDoc()
    {

        $views = new stdClass();
        $views->byEventType = new StdClass;
        $views->byEventType->map = "function(doc) {
             emit([doc.type, doc.className, doc.scenario.corePath, doc.arg, doc.winner, doc.docId,doc.type,   doc.time ], doc);
        }";
        $views->byEventType->reduce = "function(keys, values, rereduce) {
                if (rereduce) {
                    return sum(values);
                } else {
                    return values.length;
                }
            }";

        $views->byGameName = new StdClass;

        $views->byGameName->map = "function(doc) {
             emit([doc.className, doc.type, doc.docId,doc.type,   doc.time ], doc);
        }";
        $views->byGameName->reduce = "function(keys, values, rereduce) {
                if (rereduce) {
                    return sum(values);
                } else {
                    return values.length;
                }
            }";


        $doc = false;
        $data = array("_id" => "_design/gameEvents", "views" => $views);
        try {
            $doc = $this->couchsag->get("_design/gameEvents");
        } catch (Exception $e) {
        };
        if ($doc) {
            echo "Doc Found deleting: _design/gameEvents\n";
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
