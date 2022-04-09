<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class Kq2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kq2 = DB::select("SELECT s.KQ2_ID_MEDIO_ACCESO,s.KQ2_ID_MEDIO_ACCESO_DES,s.MONTOA,s.TXSA,w.MONTOR,w.TXSR FROM (
        select t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES,sum(t.MONTO1) AS MONTOA, count(*) as TXSA 
        from medioacceso as e inner join test as t on e.KQ2_ID_MEDIO_ACCESO = t.KQ2_ID_MEDIO_ACCESO
        where t.CODIGO_RESPUESTA < '010'
        group by t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES) as s
        inner join
        (
        select t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES,sum(t.MONTO1) AS MONTOR, count(*) as TXSR 
        from medioacceso as e inner join test as t on e.KQ2_ID_MEDIO_ACCESO = t.KQ2_ID_MEDIO_ACCESO
        where t.CODIGO_RESPUESTA >= '010'
        group by t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES) as w on s.KQ2_ID_MEDIO_ACCESO = w.KQ2_ID_MEDIO_ACCESO");
        $array = json_decode(json_encode($kq2), true); //Codificar un array asociativo

        $totalTX = 0;

        foreach($array as $keyTotal => $data){
            $totalTX += $data['TXSA'] + $data['TXSR'];
        }

        $answer = array();
        
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> ID = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> Description = $data['KQ2_ID_MEDIO_ACCESO_DES'];
            $answer[$key] -> TX_Accepted = number_format($data['TXSA']);
            $answer[$key] -> TX_Rejected = number_format($data['TXSR']);
            $answer[$key] -> accepted_Amount = number_format($data['MONTOA'], 2, '.');
            $answer[$key] -> rejected_Amount = number_format($data['MONTOR'], 2, '.'); 
            $answer[$key] -> percenTX_Accepted = round((($data['TXSA'] / $totalTX) * 100), 2);
            $answer[$key] -> percenTX_Rejected = round((($data['TXSR'] / $totalTX) * 100), 2);
        }
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }

    public function filterKq2(Request $request){

        $kq2filter = $request -> kq2;
        $totalTX = 0;
        $answer = array();

        $kq2 = DB::select("SELECT s.KQ2_ID_MEDIO_ACCESO,s.KQ2_ID_MEDIO_ACCESO_DES,s.MONTOA,s.TXSA,w.MONTOR,w.TXSR FROM (
            select t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES,sum(t.MONTO1) AS MONTOA, count(*) as TXSA 
            from medioacceso as e inner join test as t on e.KQ2_ID_MEDIO_ACCESO = t.KQ2_ID_MEDIO_ACCESO
            where t.CODIGO_RESPUESTA < '010'
            group by t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES) as s
            inner join
            (
            select t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES,sum(t.MONTO1) AS MONTOR, count(*) as TXSR 
            from medioacceso as e inner join test as t on e.KQ2_ID_MEDIO_ACCESO = t.KQ2_ID_MEDIO_ACCESO
            where t.CODIGO_RESPUESTA >= '010'
            group by t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES) as w on s.KQ2_ID_MEDIO_ACCESO = w.KQ2_ID_MEDIO_ACCESO");
            $array = json_decode(json_encode($kq2), true); //Codificar un array asociativo

        foreach($array as $keyTotal => $data){
            $totalTX += $data['TXSA'] + $data['TXSR'];
        }

        foreach($array as $key => $data){
            if($data['KQ2_ID_MEDIO_ACCESO'] == $kq2filter){
                $answer[$key] = new stdClass();
                $answer[$key] -> ID = $data['KQ2_ID_MEDIO_ACCESO'];
                $answer[$key] -> Description = $data['KQ2_ID_MEDIO_ACCESO_DES'];
                $answer[$key] -> TX_Accepted = number_format($data['TXSA']);
                $answer[$key] -> TX_Rejected = number_format($data['TXSR']);
                $answer[$key] -> accepted_Amount = number_format($data['MONTOA'], 2, '.');
                $answer[$key] -> rejected_Amount = number_format($data['MONTOR'], 2, '.'); 
                $answer[$key] -> percenTX_Accepted = round((($data['TXSA'] / $totalTX) * 100), 2);
                $answer[$key] -> percenTX_Rejected = round((($data['TXSR'] / $totalTX) * 100), 2);
            }
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        $arrayJSONOrdened = array_values($arrayJSON);
        return $arrayJSONOrdened;
    }
}