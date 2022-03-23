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
        $dashboard = DB::select("select CODIGO_RESPUESTA,TIPO,KQ2_ID_MEDIO_ACCESO,sum(MONTO1) AS MONTO, 
        count(*) as TXS from test group by CODIGO_RESPUESTA,TIPO,KQ2_ID_MEDIO_ACCESO");
        $array = json_decode(json_encode($dashboard), true); //Codificar un array asociativo

        $answer = array();

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> Type = $data['TIPO'];
            $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> amount = $data['MONTO'];
            $answer[$key] -> tx = $data['TXS'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }
}
