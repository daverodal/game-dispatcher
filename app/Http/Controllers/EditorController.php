<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AdminService;
use App\Services\CouchService;
use App\Http\Requests;

class EditorController extends Controller
{
    public function __construct()
    {
        $this->middleware('editor');

    }

    public function index(){
        return view('layouts.editor');
    }
    public function areaMapEdit(){
        $backgroundImage = "BWP-1_Baltops_2016_0283.jpg";
        $backgroundAttr = '<a rel="nofollow" target="_blank" class="external text" href="http://konflikty.pl">Konflikty.pl</a> [Attribution or Attribution], <a target="_blank" href="https://commons.wikimedia.org/wiki/File%3ABWP-1_Baltops_2016_0283.jpg">via Wikimedia Commons</a>';

        return view('area-map-edit/area-map-edit',compact("backgroundImage", "backgroundAttr"));
    }
}
