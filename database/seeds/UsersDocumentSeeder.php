<?php

use Illuminate\Database\Seeder;

class UsersDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conf = \Config::get('couch');
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
        $guz->setDb('games');
        $this->couchsag = $guz;
        $this->initDoc();
        $this->makeLogins();
        $this->makeGamesAvail();
    }

    public function initDoc()
    {
        $views = new stdClass();

        $usersByEmail = new StdClass();
        $usersByEmail->map = <<<aHEREMAP
        function(doc) {
            if(doc.docType == 'users'){
                var ret = 0;

                if(doc.userByEmail){
                    for(var email in doc.userByEmail){
                        emit(email,doc.userByEmail[email]);
                    }
                }
            }
        }
aHEREMAP;
        $userById = new stdClass();
        $userById->map = <<<byId
        function(doc) {
            if(doc.docType == 'users'){
                var ret = 0;

                if(doc.userByEmail){
                    var aThing;
                    for(var email in doc.userByEmail){
                        aUser = doc.userByEmail[email];
                        theUser = {};
                        for(x in aUser){
                            theUser[x] = aUser[x];
                        }
                        theUser.email = email;
                        emit(doc.userByEmail[email].id,theUser);
                    }
                }
            }
        }
byId;
        $userByUsername = new stdClass();
        $userByUsername->map = <<<byUsername
        function(doc) {
            if(doc.docType == 'users'){
                var ret = 0;

                if(doc.userByEmail){
                    for(var email in doc.userByEmail){
                        aUser = doc.userByEmail[email];
                        theUser = {};
                        for(x in aUser){
                            theUser[x] = aUser[x];
                        }
                        theUser.email = email;
                        emit(doc.userByEmail[email].username,theUser);
                    }
                }
            }
        }
byUsername;


        $getCustomScenarios = new stdClass();
        $getCustomScenarios->map = <<<getCustomScenarios
          function(doc){
                if(doc.docType == 'scenariosAvail'){
                    if(doc.games){
                        for(var i in doc.games){
                          for(var s in doc.games[i].scenarios){
                            emit([doc.games[i].path,doc.games[i].genre,i,s],doc.games[i].scenarios[s]);
                          }
                        }
                    }
                }
            }
getCustomScenarios;

        $gnuGetAvailGames = new stdClass();
        $gnuGetAvailGames->map = <<<getAvailGames
     function(doc){
                if(doc.docType == 'gamesAvail'){
                    if(doc.games){
                        for(var i in doc.games){
                            emit([doc.games[i].path,doc.games[i].genre,i],doc.games[i]);
                        }
                    }
                }
            }
getAvailGames;

        $gnuGetAvailGames->reduce = <<<getGamesAvailableReduce
        function(keys, values, rereduce) {
                if (rereduce) {
                    return sum(values);
                } else {
                    return values.length;
                }
            }
getGamesAvailableReduce;




        $views->userByEmail = $usersByEmail;
        $views->userById = $userById;
        $views->userByUsername = $userByUsername;
        $views->getCustomScenarios = $getCustomScenarios;
        $views->getAvailGames = $gnuGetAvailGames;


        $this->couchsag->setDb('users');


        $doc = false;
        $data = array("_id" => "_design/newFilter", "views" => $views);
        try {
            $doc = $this->couchsag->get("_design/newFilter");
        } catch (Exception $e) {
        };
        if ($doc) {
            echo "Doc Found deleting: _design/newFilter\n";
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

    public function makeLogins(){
        $doc = false;
        $this->couchsag->setDb('users');

        try{
            echo "is userLogins doc presesnt?\n";
            $doc = $this->couchsag->get("userLogins");
        }catch(Exception $e){};
        if(!$doc){
            $data = array("_id" => "userLogins", "docType" => "userLogins", "logins" => array());
            echo "createing userLogins\n";
            $this->couchsag->post($data);
            echo "Created them\n";
        }else{
            echo "userLogins doc found, leaving untouched\n";
        }
    }

    public function makeGamesAvail(){
        $doc = false;
        $this->couchsag->setDb('users');

        try{
            echo "is gamesAvail doc presesnt?\n";
            $doc = $this->couchsag->get("gamesAvail");
            var_dump($doc);
            $this->couchsag->delete($doc->_id,$doc->_rev);
            echo "deleted it";
        }catch(Exception $e){};
        if(true || !$doc){
            $data = array("_id" => "gamesAvail", "docType" => "gamesAvail", "games" => new stdClass());
            echo "createing gamesAvail\n";
            $this->couchsag->post($data);
            var_dump($data);
            echo "Created them\n";
        }else{
            var_dump($doc);
            echo "gamesAvail doc found, leaving untouched\n";
        }
    }
}
