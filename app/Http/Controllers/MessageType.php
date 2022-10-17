<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class MessageType extends Controller
{
    public function index(){
        $answer = array();
        $respJson = array();

        $response = DB::select('select * from catalogo_message_type');
        $respJson = json_decode(json_encode($response), true);

        foreach($respJson as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> id = $data['ID'];
            $answer[$key] -> desp = $data['DESCRIPTION'];
        }
        $responseJSON = json_decode(json_encode($answer));
        return $responseJSON;
    }
    public function getCatalogTypeMessage(Request $request){
        $type = $request -> messageType;
        $respArr = array();
        $answer = array();

        $query = 'select * from catalogo_message_type where ID = ?';
        $response = DB::select($query, [$type]);
        $respArr = json_decode(json_encode($response), true);

        foreach($respArr as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> typeMess = $data['ID'].' - '.$data['DESCRIPTION'];
        }
        $responseJSON = json_decode(json_encode($answer));
        return $responseJSON;
    }
}
