<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Test;
use stdClass;

class TestController extends Controller
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

        $totalAmount = 0;
        $totalTX = 0;
        $totalTX_Acepted = 0;
        $totalAmount_Accepted = 0;
        $totalTX_Rejected = 0;
        $totalAmount_Rejected = 0;

        foreach($array as $key => $data){
            //Monto total y transacciones totales.
            $totalAmount += $data['MONTO'];
            $totalTX += $data['TXS'];

            //Aceptadas y Rechazadas TXs y Monto
            if($data['CODIGO_RESPUESTA'] <= '010'){
                $totalTX_Acepted += $data['TXS'];
                $totalAmount_Accepted += $data['MONTO'];
            }else{
                $totalTX_Rejected += $data['TXS'];
                $totalAmount_Rejected += $data['MONTO'];
            }
        }
        $answer = new Test();
        $answer -> totalAmount = $totalAmount;
        $answer -> totalTX = $totalTX;
        $answer -> totalTX_Accepted = $totalTX_Acepted;
        $answer -> totalAmount_Accepted = $totalAmount_Accepted;
        $answer -> totalTX_Rejected = $totalTX_Rejected;
        $answer -> totalAmount_Rejected = $totalAmount_Rejected;
        $answer -> percenAccepted = round((($totalTX_Acepted/$totalTX)*100), 2);
        $answer -> percenRejected = round((($totalTX_Rejected/$totalTX)*100), 2);

        return $answer;
    }
}
