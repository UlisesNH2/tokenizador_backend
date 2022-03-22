<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class TokenC0Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tokenC0 = DB::select('select KC0_INDICADOR_DE_COMERCIO_ELEC,KC0_TIPO_DE_TARJETA, 
        KC0_INDICADOR_DE_CVV2_CVC2_PRE, KC0_INDICADOR_DE_INFORMACION_A from test');
        $array = json_decode(json_encode($tokenC0), true);

        $answer = array();

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> ID_Ecommerce = $data['KC0_INDICADOR_DE_COMERCIO_ELEC'];
            $answer[$key] -> Card_Type = $data['KC0_TIPO_DE_TARJETA'];
            $answer[$key] -> ID_CVV2 = $data['KC0_INDICADOR_DE_CVV2_CVC2_PRE'];
            $answer[$key] -> ID_Information = $data['KC0_INDICADOR_DE_INFORMACION_A'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }

    public function getDataTableFilter(Request $request)
    {
        $values = array();
        $label = ['KC0_INDICADOR_DE_COMERCIO_ELEC', 'KC0_TIPO_DE_TARJETA', 'KC0_INDICADOR_DE_CVV2_CVC2_PRE',
        'KC0_INDICADOR_DE_INFORMACION_A'];

        $values[0] = $request -> ID_Ecommerce;
        $values[1] = $request -> Card_Type;
        $values[2] = $request -> ID_CVV2;
        $values[3] = $request -> ID_Information;

        $answer = array();

        for($key = 0; $key < 4; $key++){
            if($values[$key] == "NonValue"){
                unset($values[$key]);
                unset($label[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($label);

        for($key = 0; $key < sizeof($filteredValues); $key++){
            $response = DB::select("select FIID_TARJ,FIID_COMER,NOMBRE_DE_TERMINAL,CODIGO_RESPUESTA,R,
            NUM_SEC,KQ2_ID_MEDIO_ACCESO,ENTRY_MODE,MONTO1 from test where ".$filteredLabels[$key]." = '".$filteredValues[$key]."'");
            $array = json_decode(json_encode($response), true);
        }
        foreach($array as $key => $data){
            if($data['CODIGO_RESPUESTA'] > 010){
                $answer[$key] = new stdClass();
                $answer[$key] -> Fiid_Card = $data['FIID_TARJ'];
                $answer[$key] -> Fiid_Comerce = $data['FIID_COMER'];
                $answer[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
                $answer[$key] -> Code_Response = $data['CODIGO_RESPUESTA'];
                $answer[$key] -> R = $data['R'];
                $answer[$key] -> Number_Sec = $data['NUM_SEC'];
                $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                $answer[$key] -> entryMode = $data['ENTRY_MODE'];
                $answer[$key] -> amount = number_format($data['MONTO1'], 2, '.');
            }
        }
        foreach($array as $key => $data){
            if($data['CODIGO_RESPUESTA'] < 010){
                $answer[$key] = new stdClass();
                $answer[$key] -> Fiid_Card = $data['FIID_TARJ'];
                $answer[$key] -> Fiid_Comerce = $data['FIID_COMER'];
                $answer[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
                $answer[$key] -> Code_Response = $data['CODIGO_RESPUESTA'];
                $answer[$key] -> R = $data['R'];
                $answer[$key] -> Number_Sec = $data['NUM_SEC'];
                $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                $answer[$key] -> entryMode = $data['ENTRY_MODE'];
                $answer[$key] -> amount = number_format($data['MONTO1'], 2, '.');
            }
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        $arrayJSONOrdered = array_values($arrayJSON);
        return $arrayJSONOrdered;
    }
}
