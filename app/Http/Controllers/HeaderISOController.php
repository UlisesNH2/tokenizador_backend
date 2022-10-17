<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class HeaderISOController extends Controller{

    public function getCatalogHdrMess(Request $request){
        $respArry = array();
        $answer = array();

        $typeMess = $request -> messageType;
        $query = "select * from cat_expetedval_hdriso where ID = ?";
        $response = DB::select($query, [$typeMess]);
        $respArry = json_decode(json_encode($response), true);

        foreach($respArry as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> id = $data['ID'];
            $answer[$key] -> productId = explode(',', $data['PRODUCT_ID']); //explode() -> para convertir un string (valores separados por comas) en un arrelo
            $answer[$key] -> relaseNuber = explode(',', $data['RELASE_NUMBER']);
            $answer[$key] -> status = explode(',', $data['STATUS']);
            $answer[$key] -> originCode = explode(',', $data['ORIGIN_CODE']);
            $answer[$key] -> responserCode = explode(',', $data['RESPONSER_CODE']);
        }
        $responseJSON = json_decode(json_encode($answer));
        return $responseJSON;
    }
}