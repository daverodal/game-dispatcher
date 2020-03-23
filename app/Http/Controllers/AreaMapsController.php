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
            'boxes.*.x' => 'required'
        ]);
        $data = new \stdClass();
        $data->docType = "areaMapData";
        $data->boxes = $postData['boxes'];
        $data->name = $postData['name'];
        $data->url = $postData['url'];
        $data->neighbors = $postData['neighbors'];
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
                'boxes.*.x' => 'required'
            ]);
            $doc->boxes = $postData['boxes'];
            $doc->url = $postData['url'];
            $doc->name = $postData['name'];
            $client->put($id,$doc);
            $postData['map']['id'] = $id;
            return response()->json($postData);

        }
}