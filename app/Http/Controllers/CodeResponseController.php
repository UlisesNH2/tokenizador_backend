<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class codeResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $codeResponse = DB::select("select t.CODIGO_RESPUESTA,c.Codigo_Respuesta_Des,sum(t.MONTO1) AS MONTO, count(*) as TXS 
        from test as t inner join codrespuesta as c on t.CODIGO_RESPUESTA = c.CODIGO_RESPUESTA 
        group by CODIGO_RESPUESTA,c.Codigo_Respuesta_Des");
        $array = json_decode(json_encode($codeResponse), true); //Codificar un array asociativo
        
        $totalTX = 0;

        foreach($array as $keyTotal => $data){
            $totalTX += $data['TXS'];
        }

        $answer = array();

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> ID = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> Description = $data['Codigo_Respuesta_Des'];
            $answer[$key] -> CodeResp_Amount = number_format($data['MONTO'], 2, '.');
            $answer[$key] -> CodeResp_TXS = number_format($data['TXS']);
            $answer[$key] -> CodeResp_Percent = round(($data['TXS'] / $totalTX * 100), 2);
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo

        return $arrayJson;
    }
}
