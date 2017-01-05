<?php

use Illuminate\Database\Seeder;
use App\Services\CouchService;

class ParamsDocumentSeeder extends Seeder
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
        $guz->setDb('params');
        $this->couchsag = $guz;
        $this->initDoc();
    }

    public function initDoc()
    {

        $views = new stdClass();
        $views->byClassName = new StdClass;
        $views->byClassName->map = "function(doc) {
            emit([doc.className, doc.arg, doc.attackingForceId], doc.history);
            }";
        $views->byClassName->reduce = "function(keys, values, rereduce) {
                if (rereduce) {
                    return sum(values);
                } else {
                    return values.length;
                }
            }";

        $views->getBugReports = new StdClass();
        $views->getBugReports->map = "function(doc){
            if(doc.docType === 'bug-report'){
                        emit([doc.className, doc.arg, doc.attackingForceId], [doc.history.length,doc.msg]);
                }
            }";

        $doc = false;
        $data = array("_id" => "_design/paramEvents", "views" => $views);
        try {
            $doc = $this->couchsag->get("_design/paramEvents");
        } catch (Exception $e) {
        };
        if ($doc) {
            echo "Doc Found deleting: _design/paramEvents\n";
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
