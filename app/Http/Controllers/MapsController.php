<?php

namespace App\Http\Controllers;
use App\Services\CouchService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MapsController extends BaseController
{

    public function getIndex(){

    }

    public function getMaps(CouchService $client ){
        $seq = $client->get('_design/restFilter/_view/getMaps');

        $rows = $seq->rows;
        $maps = [];
        foreach ($rows as $key => $val) {
            $map = $val->value->map;
            $map->id = $val->key;
            $maps[] = $map;
        }
        echo json_encode(['maps' => $maps]);

    }


    function getHexstrs(CouchService $client, $stuf)
    {
        if ($stuf) {
            $doc = $client->get("$stuf");
            $doc->hexStr->id = $stuf;
            echo json_encode(['hexStr' => $doc->hexStr]);
            return;
        }
        $seq = $this->rest_model->getHexStrs();
        $rows = $seq->rows;
        $hexStrs = [];
        foreach ($rows as $key => $val) {
            $hexStr = $val->value->hexStr;
            $hexStr->id = $val->key;
            $hexStrs[] = $hexStr;
        }
        echo json_encode(['hexStrs' => $hexStrs]);
    }

    public function putMaps(CouchService $client , Request $request, $id)
    {

        $doc = $client->get($id);
        $postData = $request->all();

        $doc->map = $postData['map'];
        $client->put($id,$doc);
        $postData['map']['id'] = $id;
        echo json_encode($postData);
    }


    public function postMaps(CouchService $client , Request $request)
    {

        $postData = $request->all();

        $data = new \stdClass();
        $data->docType = "hexMapData";
        $data->map = $postData['map'];
        $resp = $client->post($data);
        $postData['map']['id'] = $resp->id;
        return json_encode($postData);
    }


    public function putHexstrs(CouchService $client , Request $request, $id){


        $doc = $client->get($id);
        $postData = $request->all();

        $doc->hexStr = $postData['hexStr'];
        $postData['hexStr']['id'] = $id;
        $client->put("$id",$doc);
        echo json_encode($postData);
    }


    public function postHexstrs(CouchService $client , Request $request)
    {
        $postData = $request->all();
        $data = new \stdClass();
        $data->docType = "hexMapStrs";
        $data->hexStr = $postData['hexStr'];
        $resp = $client->post($data);
        $postData['hexStr']['id'] = $resp->id;
        return json_encode($postData);
    }

    public function deleteHexstrs(CouchService $client, $id){
        try{
            $doc = $client->get($id);
        }catch(Exception $e){
            return json_encode(new \stdClass());
        }
        $client->delete($doc->_id, $doc->_rev);
        return json_encode(new \stdClass());
    }

    public function deleteMaps(CouchService $client, $id){
        try{
            $doc = $client->get($id);
        }catch(Exception $e){
            return json_encode(new \stdClass());
        }
        $client->delete($doc->_id, $doc->_rev);
        return json_encode(new \stdClass());
    }
}
