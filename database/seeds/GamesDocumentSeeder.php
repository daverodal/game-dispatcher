<?php

use Illuminate\Database\Seeder;

class GamesDocumentSeeder extends Seeder
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
    }

    public function initDoc()
    {
        $views = new StdClass();
        $views->getGamesImIn = new StdClass;
        $views->getGamesImIn->map = "function(doc){/*comment */
            if(doc.docType == 'wargame' && doc.playerStatus == 'multi'){
                for(var i in doc.wargame.players){
                    if(doc.wargame.players[i] == '' || doc.wargame.players[i] == doc.createUser){
                        continue;
                    }
                    var gameName = doc.gameName;
                    if(doc.wargame.arg){
                        gameName += '-'+doc.wargame.arg;
                    }

                    emit([doc.wargame.players[i],doc.createUser, doc.name, gameName, doc.gameName,doc.playerStatus, doc.wargame.gameRules.attackingForceId, doc._id],[doc.gameName,doc.createDate,doc.wargame.players,doc.wargame.victory.gameOver]);
                }
            }
        }";
        $views->publicGames = new StdClass;
        $views->publicGames->map = "function(doc){/*comment */
            if(doc.docType == 'wargame' &&  doc.visibility == 'public'){
                    var gameName = doc.gameName;
                    if(doc.wargame.arg){
                        gameName += '-'+doc.wargame.arg;
                    }

                    emit([doc.createUser, doc.name, gameName, doc.gameName,doc.playerStatus, doc.wargame.gameRules.attackingForceId, doc._id],[doc.gameName,doc.createDate,doc.wargame.players]);

            }
        }";
        $views->getLobbies = new StdClass;
        $views->getLobbies->map = "function(doc){
            if(doc.docType == 'wargame'){
               var gameName = doc.gameName;
	        	if(doc.wargame.arg){
		            gameName += '-'+doc.wargame.arg;
                }
                emit([doc.createUser, doc.playerStatus, gameName,doc.name,doc.wargame.gameRules.attackingForceId, doc._id, doc.visibility],[doc.gameName,doc.createDate,doc.wargame.players,doc.wargame.mapData.mapUrl,doc.wargame.victory.gameOver,doc.wargame.gameRules.turn, doc.wargame.gameRules.maxTurn]);
            }}";
        $views->getTerrain = new StdClass;
        $views->getTerrain->map = "function(doc){
            if(doc.docType == 'terrain'){
               
                emit([doc._id],true);
            }}";
//        $views->getAvailGames = new StdClass;
//        $views->getAvailGames->map = "function(doc){if(doc.docType == 'gamesAvail'){if(doc.games){for(var i in doc.games){emit(doc.games[i],doc.games[i]);}}}}";
        $filters = new StdClass();
        $filters->namefind = <<<NameFind
            function(doc,req){
                if(!req.query.name){return false;}
                 var names = req.query.name;
                 if(doc._id == names){
                    return true;
                 }
                 return false;
            }
NameFind;
        $filters->lobbyChanges = <<<LobbyChanges
        function(doc,req){
            if(doc._deleted === true){
                return true;
            }
            if(!req.query.name){
                return false;
            }
            var player = req.query.name;
            if(doc.docType != "wargame"){
                return false;
            }
            if(doc.playerStatus == "created" && doc.createUser == req.query.name){
                return true;
            }
            if(typeof(doc.wargame) == 'undefined'){
                return false;
            }

            if(doc.visibility !== "public" && doc.createUser != player && doc.wargame.players[1] != player && doc.wargame.players[2] != player){
                return false;
            }
            if(doc.wargame.gameRules.turnChange){
                return true;
            }
            return false;
       }
LobbyChanges;

        $users = new StdClass();
        $users->map = <<<aHEREMAP
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

        $views->allGames = new stdClass();
        $views->allGames->map = "function(doc){
            if(doc.docType == 'wargame'){
               var gameName = doc.gameName;
	        	if(doc.wargame && doc.wargame.arg){
		            gameName += '-'+doc.wargame.arg;
                }
                emit([doc.createUser,gameName,doc.name,doc.playerStatus,doc.createDate, doc._id],[doc.gameName,doc.createDate]);
            }}";
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
        $wargame = new StdClass();
        $wargame->map = <<<HEREMAP
        function(doc) {
            if(doc.docType == 'game' || doc.docType == 'wargame'){
                var ret = 0;

                if(doc.users ){
                    ret = doc.users.length;
                }
                emit([doc.docType,doc._id],ret);
            }
        }
HEREMAP;
        $wargame->reduce = <<<HERE
function(keys,values){return sum(values);}
HERE;
        $update = <<<HEREUPDATE
function(doc,req){
    doc.chats.push(req.query.chat);
    doc.chats_index++;
    doc.chitty = "ssssss";
    return [doc,"done"];
}
HEREUPDATE;


        $updates = new StdClass();

//        $updates->addchat = $update;
//        $views->wargame = $wargame;
//        $views->userByEmail = $users;
//        $views->userById = $userById;
//        $views->userByUsername = $userByUsername;

//        $this->couchsag->sag->setDatabase('users');


        $doc = false;
        $data = array("_id" => "_design/newFilter", "views" => $views, "filters" => $filters, "updates" => $updates);
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
            // TODO add curl -X PUT -d "2500" https://localhost:5984/testdb/_revs_limit to set revs limit to 2500
            $revs = 2500;
            echo "Setting revs limit $revs\n";
            $this->couchsag->put("_revs_limit", $revs);
            echo "Writing design docs\n";
            $this->couchsag->put($data['_id'], $data);
        } catch (Exception $e) {
            echo "Died ".$e->getMessage();
        }
    }
}
