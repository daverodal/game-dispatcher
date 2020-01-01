<?php
/**
 * Copyright 2016 David Rodal
 * User: David Markarian Rodal
 * Date: 1/5/16
 * Time: 8:13 PM
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

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\AdminService;
use App\Services\CouchService;
use App\Http\Requests;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function anyIndex()
    {
        return view('admin.home');
    }

    public function getLogins(AdminService $ad){
        $logins = $ad->getLogins();
        return view('admin.logins',['logins'=>$logins->logins]);
    }

    public function getUsers(AdminService $ad){
        $users = $ad->getUsersByUsername();
        return view('admin.users',['users'=>$users]);
    }

    function getGames(AdminService $ad)
    {
        $games = $ad->getAvailGames(true);
        return view('admin.games', ['games' => $games]);
    }

    function getAllgames(AdminService $ad){

        $lobbies = $ad->getAllGames();
        return view('admin.allGames',['lobbies'=>$lobbies]);
    }


    function getDeleteBug(CouchService $cs, $gameName){

        $user = Auth::user()['name'];
        $cs->setDb('params');
        if ($gameName) {
            try {
                $doc = $cs->get($gameName);
                if (Auth::user()['is_admin']) {
                    if ($doc && $doc->_id && $doc->_rev) {
                        $cs->delete($doc->_id, $doc->_rev);
                    }
                }
            } catch (Exception $e) {
            }
        }
        return redirect('/admin/allbugs');
    }

    function getAllbugs(AdminService $ad){
        $lobbies = $ad->getAllBugs();
        return view('admin.allBugs',['lobbies'=>$lobbies]);

    }

    function getAddGame(CouchService $cs){

       $providersPaths  = \App\Services\WargameService::getProviders();

        $dir = \Input::get('dir',false);
        if($dir){
            foreach($providersPaths as $path){
                $infoPath = "$dir/info.json";
                if(file_exists($infoPath)){
                    $info = json_decode(file_get_contents($infoPath));
                    if(!$info){
                        echo "Error bade info.json";
                        dd();
                    }
                    $this->addGame($cs, $info);
                }
            }
            return redirect('admin/games');
        }
        return view('admin.addGame',[ 'providers' => $providersPaths]);
    }


    public function addGame(CouchService $cs, $games){

        $isProduction = config('app.env') === 'production';
        $prevDb = $cs->setDb('users');
        $doc = $cs->get("gamesAvail");
        if($doc->docType == "gamesAvail"){
            foreach($games as $name => $game) {
                if(!empty($game->disabled) === true){
                    continue;
                }
                if($isProduction && empty($game->production)){
                    continue;
                }
                $doc->games->$name = $game;
            }
        }
        $cs->put($doc->_id, $doc);
        $cs->setDb($prevDb);
    }

    function getDeleteGame(CouchService $cs, $gameName){
        $user = Auth::user()['name'];
        $cs->setDb('games');
        if ($gameName) {
            try {
                $doc = $cs->get($gameName);
                if ($doc->createUser == $user || Auth::user()['is_admin']) {
                    if ($doc && $doc->_id && $doc->_rev) {
                        $cs->delete($doc->_id, $doc->_rev);
                    }
                }
            } catch (Exception $e) {
            }
        }
        return redirect('/admin/allgames');
    }

    function getDeleteGameType(AdminService $ad){
        $killGame = \Input::get('killGame',false);

        if($killGame){
            $ad->deleteGame($_GET['killGame']);
        }
        return redirect('/admin/games');
    }




    public function areaMapEdit(){
        $backgroundImage = "BWP-1_Baltops_2016_0283.jpg";
        $backgroundAttr = '<a rel="nofollow" target="_blank" class="external text" href="http://konflikty.pl">Konflikty.pl</a> [Attribution or Attribution], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3ABWP-1_Baltops_2016_0283.jpg">via Wikimedia Commons</a>';

        return view('area-map-edit/area-map-edit',compact("backgroundImage", "backgroundAttr"));
    }
}