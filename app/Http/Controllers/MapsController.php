<?php

namespace App\Http\Controllers;
use App\Services\CouchService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MapsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function getIndex(){

    }

    public function getMaps(CouchService $client ){
        return $this->fetchMaps($client);
    }


    public function fetchMaps(CouchService $client ){
        $seq = $client->get('_design/restFilter/_view/getMaps');

        $rows = $seq->rows;
        $maps = [];
        foreach ($rows as $key => $val) {
            $map = $val->value->map;
            $map->id = $val->key;
            $maps[] = $map;
        }
        return response()->json(['maps' => $maps]);

    }


    function getHexstrs(CouchService $client, $stuf)
    {
        if ($stuf) {
            $doc = $client->get("$stuf");
            $doc->hexStr->id = $stuf;
            return response()->json(['hexStr' => $doc->hexStr]);
        }
        $seq = $this->rest_model->getHexStrs();
        $rows = $seq->rows;
        $hexStrs = [];
        foreach ($rows as $key => $val) {
            $hexStr = $val->value->hexStr;
            $hexStr->id = $val->key;
            $hexStrs[] = $hexStr;
        }
        return response()->json(['hexStrs' => $hexStrs]);
    }

    public function putMaps(CouchService $client , Request $request, $id)
    {

        $doc = $client->get($id);
        $postData = $request->all();

        $doc->map = $postData['map'];
        $client->put($id,$doc);
        $postData['map']['id'] = $id;
        return response()->json($postData);
    }


    public function postMaps(CouchService $client , Request $request)
    {

        $postData = $request->all();

        $data = new \stdClass();
        $data->docType = "hexMapData";
        $data->map = $postData['map'];
        $resp = $client->post($data);
        $postData['map']['id'] = $resp->id;
        return response()->json($postData);
    }


    public function putHexstrs(CouchService $client , Request $request, $id){


        $doc = $client->get($id);
        $postData = $request->all();

        $doc->hexStr = $postData['hexStr'];
        $postData['hexStr']['id'] = $id;
        $client->put("$id",$doc);
        return response()->json($postData);
    }


    public function postHexstrs(CouchService $client , Request $request)
    {
        $postData = $request->all();
        $data = new \stdClass();
        $data->docType = "hexMapStrs";
        $data->hexStr = $postData['hexStr'];
        $resp = $client->post($data);
        $postData['hexStr']['id'] = $resp->id;
        return response()->json($postData);
    }

    public function deleteHexstrs(CouchService $client, $id){
        try{
            $doc = $client->get($id);
        }catch(\GuzzleHttp\Exception\BadResponseException $e){
            return response()->json(new \stdClass());
        }
        $client->delete($doc->_id, $doc->_rev);
        return response()->json(new \stdClass());
    }

    public function deleteMaps(CouchService $client, $id){
        try{
            $doc = $client->get($id);
        }catch(\GuzzleHttp\Exception\BadResponseException $e){
            return response()->json(new \stdClass());
        }
        $client->delete($doc->_id, $doc->_rev);
        return response()->json(new \stdClass());
    }

    function cloneFile(CouchService $client, $mapId)
    {
        $client->setDb('rest');
        $cloneRet = [];
        $doc = $client->get($mapId);
        if ($doc->docType == "hexMapData") {
            unset($doc->_id);
            unset($doc->_rev);
            $hexStr = $doc->map->hexStr;
            $doc->map->hexStr = "";
            $ret = $client->post($doc);
            $mapId = $ret->id;
            $mapRev = $ret->rev;
            if ($ret->ok === true) {
                if ($hexStr) {
                    $hexDoc = $client->get($hexStr);
                    unset($hexDoc->_id);
                    unset($hexDoc->_rev);
                    $hexDoc->hexStr->map = $mapId;
                    $hexRet = $client->post($hexDoc);
                    if ($hexRet->ok) {
                        $doc->_id = $mapId;
                        $doc->_rev = $mapRev;
                        $doc->map->hexStr = $hexRet->id;
                        $client->put($doc->_id, $doc);
                    }
                }
            }
            $cloneRet['mapId'] = $mapId;
            $cloneRet['hexid'] = $hexRet->id;
        }
        return response()->json($cloneRet);
    }

}
