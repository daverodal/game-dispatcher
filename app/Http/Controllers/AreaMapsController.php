<?php

namespace App\Http\Controllers;
use App\AreaMap;
use App\Services\CouchService;
use Illuminate\Http\Request;

class AreaMapsController extends Controller
{

    public function index(CouchService $client){
        $seq = $client->get('_design/areaMaps/_view/byMap');

        $rows = $seq->rows;
        $maps = [];
        foreach ($rows as $key => $val) {
            $map = $val->value;
            $map->id = $val->key;
            $maps[] = $map;
        }
        return ['maps' => $maps];
    }
    public function store(CouchService $client ,Request $request){
        $postData = request()->validate([
           'name' => '',
            'url' => 'url',
            'boxes' => 'array',
            'boxes.*.x' => 'required',
            'borderBoxes' => 'array',
            'borderBoxes.*.x' => 'required',
            'width' => ''
        ]);
        $data = new \stdClass();
        $data->docType = "areaMapData";
        $data->boxes = $postData['boxes'];
        $data->borderBoxes = $postData['borderBoxes'];
        $data->name = $postData['name'];
        $data->url = $postData['url'];
        $data->width = $postData['width'];
        $resp = $client->post($data);
        $postData['map']['id'] = $resp->id;
//        dd($data);
//        $areaMap = AreaMap::create($data);
        return response()->json($postData, 201);
    }

    public function show(CouchService $client ,$mapId){
        $postData = $client->get($mapId);
        return response()->json($postData, 200);
        }
        public function update(CouchService $client, Request $request, $id){

            $doc = $client->get($id);
            $postData = request()->validate([
                'name' => 'required',
                'url' => 'required|url',
                'boxes' => 'array',
                'boxes.*.x' => 'required',
                'borderBoxes' => 'array',
                'borderBoxes.*.x' => 'required',
                'width' => '',
                'gameName' => '',
                'scenarioName' => ''
            ]);
            $doc->boxes = $postData['boxes'];
            $doc->borderBoxes = $postData['borderBoxes'];
            $doc->url = $postData['url'];
            $doc->name = $postData['name'];
            $doc->width = $postData['width'];
            $doc->gameName = $postData['gameName'];
            $doc->scenarioName = $postData['scenarioName'];
            $client->put($id,$doc);
            $postData['map']['id'] = $id;
            return response()->json($postData);

        }
}
