<?php
/**
 * Created by PhpStorm.
 * User: david_rodal
 * Date: 1/7/16
 * Time: 2:49 PM
 */

namespace app\Http\Controllers;
use Illuminate\Http\Request;


class WargameController extends Controller
{

    public function getIndex(){
        return 'love';
    }

    public function getPlay(Request $req){
        var_dump($req->session()->get('wargamee'));

        return 'abc '.$req->session()->get('wargamee').' 123';
    }

    public function getSetplay(Request $req, $id){
        $req->session()->put('wargame', $id);
        return redirect('wargame/play');
    }
}