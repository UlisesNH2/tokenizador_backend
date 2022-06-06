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
        $kq2 = DB::select("select accepted.KQ2_ID_MEDIO_ACCESO, accepted.KQ2_ID_MEDIO_ACCESO_DES, accepted.MONTOA, accepted.TXSA, rejected.MONTOR, rejected.TXSR FROM 
        (select main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES, sum(main.MONTO1) AS MONTOA, count(*) as TXSA 
        from medioacceso as kq2 inner join test as main on kq2.KQ2_ID_MEDIO_ACCESO = main.KQ2_ID_MEDIO_ACCESO
        where main.CODIGO_RESPUESTA < '010' group by main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES) as accepted
        inner join
        (select main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES, sum(main.MONTO1) AS MONTOR, count(*) as TXSR 
        from medioacceso as kq2 inner join test as main on kq2.KQ2_ID_MEDIO_ACCESO = main.KQ2_ID_MEDIO_ACCESO
        where main.CODIGO_RESPUESTA >= '010' group by main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES) as rejected 
        on accepted.KQ2_ID_MEDIO_ACCESO = rejected.KQ2_ID_MEDIO_ACCESO ORDER BY accepted.KQ2_ID_MEDIO_ACCESO");
        $array = json_decode(json_encode($kq2), true); //Codificar un array asociativo
        $answer = array();
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> ID = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> Description = $data['KQ2_ID_MEDIO_ACCESO_DES'];
        }
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }

    public function filterKq2(Request $request){

        $kq2Filter = $request -> kq2;
        $totalTX = 0;
        $response = array();
        $answer = array();
        //Query en caso de que no exista algún filtro
        $query = "select accepted.KQ2_ID_MEDIO_ACCESO, accepted.KQ2_ID_MEDIO_ACCESO_DES, accepted.MONTOA, accepted.TXSA, rejected.MONTOR, rejected.TXSR FROM 
        (select main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES, sum(main.MONTO1) AS MONTOA, count(*) as TXSA 
        from medioacceso as kq2 inner join test as main on kq2.KQ2_ID_MEDIO_ACCESO = main.KQ2_ID_MEDIO_ACCESO
        where main.CODIGO_RESPUESTA < '010' group by main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES) as accepted
        inner join
        (select main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES, sum(main.MONTO1) AS MONTOR, count(*) as TXSR 
        from medioacceso as kq2 inner join test as main on kq2.KQ2_ID_MEDIO_ACCESO = main.KQ2_ID_MEDIO_ACCESO
        where main.CODIGO_RESPUESTA >= '010' group by main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES) as rejected 
        on accepted.KQ2_ID_MEDIO_ACCESO = rejected.KQ2_ID_MEDIO_ACCESO";

        //Query modificado para obtener los valores decuerdo al filtro
        $queryFilter = "select accepted.KQ2_ID_MEDIO_ACCESO, accepted.KQ2_ID_MEDIO_ACCESO_DES, accepted.MONTOA, accepted.TXSA, rejected.MONTOR, rejected.TXSR FROM 
        (select main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES, sum(main.MONTO1) AS MONTOA, count(*) as TXSA 
        from medioacceso as kq2 inner join test as main on kq2.KQ2_ID_MEDIO_ACCESO = main.KQ2_ID_MEDIO_ACCESO
        where main.CODIGO_RESPUESTA < '010' and main.kq2_ID_MEDIO_ACCESO = ? group by main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES) as accepted
        inner join
        (select main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES, sum(main.MONTO1) AS MONTOR, count(*) as TXSR 
        from medioacceso as kq2 inner join test as main on kq2.KQ2_ID_MEDIO_ACCESO = main.KQ2_ID_MEDIO_ACCESO
        where main.CODIGO_RESPUESTA >= '010' and main.kq2_ID_MEDIO_ACCESO = ? group by main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES) as rejected 
        on accepted.KQ2_ID_MEDIO_ACCESO = rejected.KQ2_ID_MEDIO_ACCESO";

        if(!empty($kq2Filter)){
            for($i = 0; $i < count($kq2Filter); $i++){
                $response = array_merge($response, DB::select($queryFilter, [$kq2Filter[$i], $kq2Filter[$i]]));
            }
            $array = json_decode(json_encode($response), true);
        }else{
            $response = array_merge($response, DB::select($query));
            $array = json_decode(json_encode($response), true);
        }

        foreach($array as $key => $data){
            $totalTX += $data['TXSA'] + $data['TXSR'];
        }

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> ID = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> Description = $data['KQ2_ID_MEDIO_ACCESO_DES'];
            $answer[$key] -> TX_Accepted = number_format($data['TXSA']);
            $answer[$key] -> TX_Rejected = number_format($data['TXSR']);
            //Separación decimal y entero de ambos montos
            $decAccepted = substr($data['MONTOA'], strlen($data['MONTOA'])-2, 2);
            $intAccepted = substr($data['MONTOA'], 0, strlen($data['MONTOA'])-2);
            $answer[$key] -> accepted_Amount = '$'.number_format($intAccepted.".".$decAccepted, 2);
            $decRejected = substr($data['MONTOR'], strlen($data['MONTOA'])-2, 2);
            $intRejected = substr($data['MONTOR'], 0, strlen($data['MONTOR'])-2);
            $answer[$key] -> rejected_Amount = '$'.number_format($intRejected.".".$decRejected, 2);
            $answer[$key] -> percenTX_Accepted = round((($data['TXSA'] / $totalTX) * 100), 2).'%';
            $answer[$key] -> percenTX_Rejected = round((($data['TXSR'] / $totalTX) * 100), 2).'%';
        }
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }
}
