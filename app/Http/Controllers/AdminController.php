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
        $games = $ad->availGames(true);
        return view('admin.games', ['games' => $games]);
    }

    function getAllgames(AdminService $ad){

        $lobbies = $ad->getAllGames();
        return view('admin.allGames',['lobbies'=>$lobbies,'peace'=>'love']);
    }


}