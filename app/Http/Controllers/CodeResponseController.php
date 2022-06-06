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
        $codeResponse = DB::select("select main.CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES 
        from test as main inner join codrespuesta as code on main.CODIGO_RESPUESTA = code.CODIGO_RESPUESTA 
        group by CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES");
        $array = json_decode(json_encode($codeResponse), true); //Codificar un array asociativo
        $answer = array();
        foreach ($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID = $data['CODIGO_RESPUESTA'];
            $answer[$key]->Description = $data['CODIGO_RESPUESTA_DES'];
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }

    public function filterCodeResponse(Request $request)
    {
        $codeResponse = $request->codeResponse;
        $response = array();
        $answer = array();
        $totalTX = 0;

        $query = "select main.CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES, sum(main.MONTO1) AS MONTO, count(*) as TXS 
        from test as main inner join codrespuesta as code on main.CODIGO_RESPUESTA = code.CODIGO_RESPUESTA
        group by CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES";

        $queryFilter = "select main.CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES, sum(main.MONTO1) AS MONTO, count(*) as TXS 
        from test as main inner join codrespuesta as code on main.CODIGO_RESPUESTA = code.CODIGO_RESPUESTA where main.CODIGO_RESPUESTA = ?
        group by CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES";

        if(!empty($codeResponse)){
            for($i = 0; $i < count($codeResponse); $i++){
                $response = array_merge($response, DB::select($queryFilter, [$codeResponse[$i]]));
            }
            $array = json_decode(json_encode($response), true);
        }else{
            $response = array_merge($response, DB::select($query));
            $array = json_decode(json_encode($response), true);
        }

        foreach ($array as $key => $data) {
            $totalTX += $data['TXS'];
        }

        foreach ($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID = $data['CODIGO_RESPUESTA'];
            $answer[$key]->Description = $data['CODIGO_RESPUESTA_DES'];
            //Separación de cifra decimal y entera para el monto
            $dec = substr($data['MONTO'], strlen($data['MONTO'])-2, 2);
            $int = substr($data['MONTO'], 0, strlen($data['MONTO'])-2);
            $answer[$key]->CodeResp_Amount = '$'.number_format($int.".".$dec, 2);
            $answer[$key]->CodeResp_TXS = number_format($data['TXS']);
            $answer[$key]->CodeResp_Percent = round(($data['TXS'] / $totalTX * 100), 2).'%';
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }
}
