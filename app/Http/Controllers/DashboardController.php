<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dashboard = DB::select("select CODIGO_RESPUESTA,TIPO,KQ2_ID_MEDIO_ACCESO, ENTRY_MODE, sum(MONTO1) AS MONTO, 
        count(*) as TXS from test group by CODIGO_RESPUESTA,TIPO,KQ2_ID_MEDIO_ACCESO, ENTRY_MODE");
        $array = json_decode(json_encode($dashboard), true); //Codificar un array asociativo

        $answer = array();

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> Type = $data['TIPO'];
            $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
            $answer[$key] -> amount = $data['MONTO'];
            $answer[$key] -> tx = $data['TXS'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }

    public function filterDashboard(Request $request){

        $label = $request -> endPoint;
        $kq2 = $request -> kq2;
        $code_Response = $request -> code_Response;
        $entry_Mode = $request -> entry_Mode;

        $dashboard = DB::select("select CODIGO_RESPUESTA,TIPO,KQ2_ID_MEDIO_ACCESO, ENTRY_MODE, sum(MONTO1) AS MONTO, 
        count(*) as TXS from test group by CODIGO_RESPUESTA,TIPO,KQ2_ID_MEDIO_ACCESO, ENTRY_MODE");
        $array = json_decode(json_encode($dashboard), true); //Codificar un array asociativo

        $answer = array();

        foreach($array as $key => $data){
            switch($label){
                case 'kq2':{
                    if($data['KQ2_ID_MEDIO_ACCESO'] == $kq2){
                        $answer[$key] = new stdClass();
                        $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
                        $answer[$key] -> Type = $data['TIPO'];
                        $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                        $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
                        $answer[$key] -> amount = $data['MONTO'];
                        $answer[$key] -> tx = $data['TXS'];
                    }
                    if($kq2 == 'allData'){
                        $answer[$key] = new stdClass();
                        $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
                        $answer[$key] -> Type = $data['TIPO'];
                        $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                        $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
                        $answer[$key] -> amount = $data['MONTO'];
                        $answer[$key] -> tx = $data['TXS'];
                    }
                    break;
                }
                case 'codeResponse':{
                    if($data['CODIGO_RESPUESTA'] == $code_Response){
                        $answer[$key] = new stdClass();
                        $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
                        $answer[$key] -> Type = $data['TIPO'];
                        $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                        $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
                        $answer[$key] -> amount = $data['MONTO'];
                        $answer[$key] -> tx = $data['TXS'];
                    }
                    if($code_Response == 'allData'){
                        $answer[$key] = new stdClass();
                        $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
                        $answer[$key] -> Type = $data['TIPO'];
                        $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                        $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
                        $answer[$key] -> amount = $data['MONTO'];
                        $answer[$key] -> tx = $data['TXS'];
                    }
                    break;
                }
                case 'entryMode':{
                    if($data['ENTRY_MODE'] == $entry_Mode){
                        $answer[$key] = new stdClass();
                        $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
                        $answer[$key] -> Type = $data['TIPO'];
                        $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                        $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
                        $answer[$key] -> amount = $data['MONTO'];
                        $answer[$key] -> tx = $data['TXS'];
                    }
                    if($entry_Mode == 'allData'){
                        $answer[$key] = new stdClass();
                        $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
                        $answer[$key] -> Type = $data['TIPO'];
                        $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                        $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
                        $answer[$key] -> amount = $data['MONTO'];
                        $answer[$key] -> tx = $data['TXS'];
                    }
                    break;
                }
                default:{
                    $answer[$key] = new stdClass();
                    $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
                    $answer[$key] -> Type = $data['TIPO'];
                    $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                    $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
                    $answer[$key] -> amount = $data['MONTO'];
                    $answer[$key] -> tx = $data['TXS'];
                }
            }
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        $arrayJSONOrdened = array_values($arrayJSON);
        return $arrayJSONOrdened;
    }

    
}
