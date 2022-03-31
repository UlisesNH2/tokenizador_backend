<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class TokenC4Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tokenC4 = DB::select("select KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
        KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
        KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD from test");
        $array = json_decode(json_encode($tokenC4), true); //Codificar un array asociativo

        $answer = array();

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> ID_Terminal_Attended = $data['KC4_TERM_ATTEND_IND'];
            $answer[$key] -> ID_Terminal = $data['KC4_TERM_OPER_IND'];
            $answer[$key] -> Terminal_Location = $data['KC4_TERM_LOC_IND'];
            $answer[$key] -> ID_Cardholder_Presence = $data['KC4_CRDHLDR_PRESENT_IND'];
            $answer[$key] -> ID_Card_Presence = $data['KC4_CRD_PRESENT_IND'];
            $answer[$key] -> ID_Card_Capture = $data['KC4_CRD_CAPTR_IND'];
            $answer[$key] -> ID_Status = $data['KC4_TXN_STAT_IND'];
            $answer[$key] -> Security_Level = $data['KC4_TXN_SEC_IND'];
            $answer[$key] -> Routing_Indicator = $data['KC4_TXN_RTN_IND'];
            $answer[$key] -> Terminal_Activation_Cardholder = $data['KC4_CRDHLDR_ACTVT_TERM_IND'];
            $answer[$key] -> ID_Terminal_Data_Transfer = $data['KC4_TERM_INPUT_CAP_IND'];
            $answer[$key] -> ID_Cardholder_Method = $data['KC4_CRDHLDR_ID_METHOD'];
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }

    public function getDataTable(){
        $tokenC4 = DB::select("select FIID_TARJ,FIID_COMER,NOMBRE_DE_TERMINAL,CODIGO_RESPUESTA,R,NUM_SEC,
        KQ2_ID_MEDIO_ACCESO,ENTRY_MODE,MONTO1 from test");
        $array = json_decode(json_encode($tokenC4), true); //Codificar array asociativo

        $answer = array();
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
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        $arrayJSONOrdered = array_values($arrayJson);
        return $arrayJSONOrdered;
    }

    public function getDataTableFilter(Request $request)
    {
        $values = array();
        $label = ['KC4_TERM_ATTEND_IND', 'KC4_TERM_OPER_IND', 'KC4_TERM_LOC_IND', 'KC4_CRDHLDR_PRESENT_IND',
                    'KC4_CRD_PRESENT_IND', 'KC4_CRD_CAPTR_IND', 'KC4_TXN_STAT_IND', 'KC4_TXN_SEC_IND', 'KC4_TXN_RTN_IND', 
                    'KC4_CRDHLDR_ACTVT_TERM_IND', 'KC4_TERM_INPUT_CAP_IND', 'KC4_CRDHLDR_ID_METHOD'];

        //No se usa estructura de control por el request
        $values[0] = $request -> ID_Terminal_Attended;
        $values[1] = $request -> ID_Terminal;
        $values[2] = $request -> Terminal_Location;
        $values[3] = $request -> ID_Cardholder_Presence; 
        $values[4] = $request -> ID_Card_Presence; 
        $values[5] = $request -> ID_Card_Capture;
        $values[6] = $request -> ID_Status;
        $values[7] = $request -> Security_Level;
        $values[8] = $request -> Routing_Indicator;
        $values[9] = $request -> Terminal_Activation_Cardholder;
        $values[10] = $request -> ID_Terminal_Data_Transfer;
        $values[11] = $request -> ID_Cardholder_Method;

        $answer = array();

        //Eliminar aquellos elementos que esten vacios para hacer la consulta
        for($key = 0; $key < 12; $key++){
            if($values[$key] == ""){
                unset($values[$key]);
                unset($label[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($label);

        for($key = 0; $key < sizeof($filteredValues); $key++){
            $response = DB::select("select FIID_TARJ,FIID_COMER,NOMBRE_DE_TERMINAL,CODIGO_RESPUESTA,R,NUM_SEC,
            KQ2_ID_MEDIO_ACCESO,ENTRY_MODE,MONTO1 from test where ".$filteredLabels[$key]." = '".$filteredValues[$key]."'");
            $array = json_decode(json_encode($response), true); //Array asociativo 
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
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        $arrayJSONOrdered = array_values($arrayJson);
        return $arrayJSONOrdered;
    }
}
