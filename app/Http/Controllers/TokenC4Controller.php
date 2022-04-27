<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class TokenC4Controller extends Controller
{
    //FUNCIÓN PARA LLEVAR TODOS LOS DATOS A LA TABLA TOKENC4
    public function index(Request $request)
    {
        $values = array();
        $labels = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE'];
        $values[0] = $request -> Kq2;
        $values[1] = $request -> Code_Response;
        $values[2] = $request -> Entry_Mode;
        $query = "select KQ2_ID_MEDIO_ACCESO, ENTRY_MODE, CODIGO_RESPUESTA, KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
        KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
        KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD from test where ";
        $array = array();
        $answer = array();

        //Eliminar values y labels que no esten filtrados desde frontend
        for($key = 0; $key < 3; $key++){
            if($values[$key] == "allData"){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);

        switch(sizeof($filteredLabels)){
            case 1: {
                $tokenC4 = DB::select($query.$filteredLabels[0]." = ?", [$filteredValues[0]]);
                $array = json_decode(json_encode($tokenC4), true); //Array asociativo
                break;
            }
            case 2: {
                $tokenC4 = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ?)",
                [$filteredValues[0], $filteredValues[1]]);
                $array = json_decode(json_encode($tokenC4), true);
                break;
            }
            case 3: {
                $tokenC4 = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ?)",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2]]);
                $array = json_decode(json_encode($tokenC4), true);
                break;
            }
            default: {
                $tokenC4 = DB:: select("select KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
                KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
                KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD from test");
                $array = json_decode(json_encode($tokenC4), true);
                break;
            }
        }

        foreach($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID_Terminal_Attended = $data['KC4_TERM_ATTEND_IND'];
            $answer[$key]->ID_Terminal = $data['KC4_TERM_OPER_IND'];
            $answer[$key]->Terminal_Location = $data['KC4_TERM_LOC_IND'];
            $answer[$key]->ID_Cardholder_Presence = $data['KC4_CRDHLDR_PRESENT_IND'];
            $answer[$key]->ID_Card_Presence = $data['KC4_CRD_PRESENT_IND'];
            $answer[$key]->ID_Card_Capture = $data['KC4_CRD_CAPTR_IND'];
            $answer[$key]->ID_Status = $data['KC4_TXN_STAT_IND'];
            $answer[$key]->Security_Level = $data['KC4_TXN_SEC_IND'];
            $answer[$key]->Routing_Indicator = $data['KC4_TXN_RTN_IND'];
            $answer[$key]->Terminal_Activation_Cardholder = $data['KC4_CRDHLDR_ACTVT_TERM_IND'];
            $answer[$key]->ID_Terminal_Data_Transfer = $data['KC4_TERM_INPUT_CAP_IND'];
            $answer[$key]->ID_Cardholder_Method = $data['KC4_CRDHLDR_ID_METHOD'];
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }

    //FUNCIÓN PARA MANDAR INFORMACIÓN A LA TABLAC4 (FILTRADA)
    public function getTableFilter(Request $request)
    {
        $values = array();
        $label = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'KC4_TERM_ATTEND_IND', 'KC4_TERM_OPER_IND', 'KC4_TERM_LOC_IND', 'KC4_CRDHLDR_PRESENT_IND',
            'KC4_CRD_PRESENT_IND', 'KC4_CRD_CAPTR_IND', 'KC4_TXN_STAT_IND', 'KC4_TXN_SEC_IND', 'KC4_TXN_RTN_IND',
            'KC4_CRDHLDR_ACTVT_TERM_IND', 'KC4_TERM_INPUT_CAP_IND', 'KC4_CRDHLDR_ID_METHOD'];

        $values[0] = $request->Kq2;
        $values[1] = $request->Code_Response;
        $values[2] = $request->Entry_Mode;
        $values[3] = $request->ID_Terminal_Attended;
        $values[4] = $request->ID_Terminal;
        $values[5] = $request->Terminal_Location;
        $values[6] = $request->ID_Cardholder_Presence;
        $values[7] = $request->ID_Card_Presence;
        $values[8] = $request->ID_Card_Capture;
        $values[9] = $request->ID_Status;
        $values[10] = $request->Security_Level;
        $values[11] = $request->Routing_Indicator;
        $values[12] = $request->Terminal_Activation_Cardholder;
        $values[13] = $request->ID_Terminal_Data_Transfer;
        $values[14] = $request->ID_Cardholder_Method;

        $answer = array();
        $response = array();
        $array = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
        KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
        KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD from test where ";

        //Eliminar values y label que no se estén filtrando
        for ($key = 0; $key < 15; $key++) {
            if ($values[$key] == "NonValue" || $values[$key] == "allData") {
                unset($values[$key]);
                unset($label[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($label);

        //Filtrado de acuerdo a las opciones elegidas en frontend
        switch (sizeof($filteredValues)) {
            case 1: {
                $response = DB::select($query . $filteredLabels[0] . " = ?", [$filteredValues[0]]);
                $array = json_decode(json_encode($response), true); //Array asociativo
                break;
            }
            case 2: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ?) and 
                (".$filteredLabels[1]." = ?)",
                [$filteredValues[0], $filteredValues[1]]);
                $array = json_decode(json_encode($response), true); //Array asociativo
                break;
            }
            case 3: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ?) and
                (".$filteredLabels[1]." = ?) and
                (".$filteredLabels[2]." = ?)",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2]]);
                $array = json_decode(json_encode($response), true); //Array asociativo
                break;
            }
            case 4: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ?) and
                (".$filteredLabels[1]." = ?) and
                (".$filteredLabels[2]." = ?) and
                (".$filteredLabels[3]." = ?)",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 5: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? )",
                [ $filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3],
                $filteredValues[4]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 6: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? )and
                (".$filteredLabels[1]." = ? )and 
                (".$filteredLabels[2]." = ? )and 
                (".$filteredLabels[3]." = ? )and 
                (".$filteredLabels[4]." = ? )and
                (".$filteredLabels[5]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], 
                $filteredValues[4], $filteredValues[5]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 7: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 8: {
                    $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 9: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and 
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and 
                (".$filteredLabels[3]." = ? ) and 
                (".$filteredLabels[4]." = ? ) and 
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 10: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 11: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? ) and
                (".$filteredLabels[10]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 12: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? ) and
                (".$filteredLabels[10]." = ? ) and
                (".$filteredLabels[11]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10], $filteredValues[11]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 13: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? ) and
                (".$filteredLabels[10]." = ? ) and
                (".$filteredLabels[11]." = ? ) and
                (".$filteredLabels[12]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10], $filteredValues[11], $filteredValues[12]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 14: {
                    $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? ) and
                (".$filteredLabels[10]." = ? ) and
                (".$filteredLabels[11]." = ? ) and
                (".$filteredLabels[12]." = ? ) and
                (".$filteredLabels[13]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10], $filteredValues[11], $filteredValues[12], $filteredValues[13]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 15: {
                $response = DB::select($query."
                (" . $filteredLabels[0] . " = ? ) and
                (" . $filteredLabels[1] . " = ? ) and
                (" . $filteredLabels[2] . " = ? ) and
                (" . $filteredLabels[3] . " = ? ) and
                (" . $filteredLabels[4] . " = ? ) and
                (" . $filteredLabels[5] . " = ? ) and
                (" . $filteredLabels[6] . " = ? ) and
                (" . $filteredLabels[7] . " = ? ) and
                (" . $filteredLabels[8] . " = ? ) and
                (" . $filteredLabels[9] . " = ? ) and
                (" . $filteredLabels[10] . " = ? ) and
                (" . $filteredLabels[11] . " = ? ) and
                (" . $filteredLabels[12] . " = ? ) and
                (" . $filteredLabels[13] . " = ? ) and
                (" . $filteredLabels[14] . " = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10], $filteredValues[11], $filteredValues[12], $filteredValues[13], $filteredValues[14]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            default: {
                $response = DB::select("select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
                KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
                KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD from test");
                $array = json_decode(json_encode($response), true);
                break;
            }
        }

        foreach ($array as $key => $data) {

            $termLocFlag = 0; $cardholdrPresFlag = 0; $cardPresenceFlag = 0; $cardholdrMethodFlag = 0;
            $termAttFlag = 0; $termActivateFlag = 0; $routingFlag = 0; $termDataTransFlag = 0;
            $termOperFlag = 0; $reqStatusFlag = 0; $secLevelFlag = 0; $cardCaptureFlag = 0;

            switch($data['KQ2_ID_MEDIO_ACCESO']){   
                case '00': {
                    //subcampo 1
                    if($data['KC4_TERM_ATTEND_IND'] == 0 || $data['KC4_TERM_ATTEND_IND'] == 1) { $termAttFlag = 1; } 
                    //subcampo 2
                    if($data['KC4_TERM_OPER_IND'] == 0) { $termOperFlag = 1; }
                    //subcampo 3
                    if($data['KC4_TERM_LOC_IND'] == 0) { $termLocFlag = 1; }
                    //subcampo 4
                    if($data['KC4_CRDHLDR_PRESENT_IND'] == 0 || $data['KC4_CRDHLDR_PRESENT_IND'] == 3) { $cardholdrPresFlag = 1; }
                    //subcampo 5
                    if($data['KC4_CRD_PRESENT_IND'] == 0) { $cardPresenceFlag = 1; }
                    //subcampo 6
                    if($data['KC4_CRD_CAPTR_IND'] == 0) { $cardCaptureFlag = 1; }
                    //subcampo 7
                    if($data['KC4_TXN_STAT_IND'] == 0) { $reqStatusFlag = 1; }
                    //subcampo 8
                    if($data['KC4_TXN_SEC_IND'] == 0 || $data['KC4_TXN_SEC_IND'] == 2) { $secLevelFlag = 1; }
                    //subcampo 9
                    if($data['KC4_TXN_RTN_IND'] == 0 || $data['KC4_TXN_RTN_IND'] == 3) { $routingFlag = 1; }
                    //subcampo 10
                    if($data['KC4_CRDHLDR_ACTVT_TERM_IND'] == 0) { $termActivateFlag = 1; }
                    //subcampo 11
                    switch($data['KC4_TERM_INPUT_CAP_IND']){
                        case 0: $termDataTransFlag = 1; break;
                        case 5: $termDataTransFlag = 1; break;
                        case 6: $termDataTransFlag = 1; break;
                        default: $termDataTransFlag = 0; break;
                    }
                    //subcampo 12
                    switch($data['KC4_CRDHLDR_ID_METHOD']){
                        case ' ': $cardholdrMethodFlag = 1; break;
                        case 0: $cardholdrMethodFlag = 1; break;
                        case 9: $cardholdrMethodFlag = 1; break;
                        default: $cardholdrMethodFlag = 0; break;
                    }
                    break;
                }
                //validación de transacciones CARGOS AUTOMÁTICOS O PERIÓDICOS.
                case '02':{
                    //subcampo 1
                    if($data['KC4_TERM_ATTEND_IND'] > -1 && $data['KC4_TERM_ATTEND_IND'] < 3) { $termAttFlag = 1; }
                    //subcampo 2
                    if($data['KC4_TERM_OPER_IND'] == 0) { $termOperFlag = 1; }
                    //subcampo 3
                    if($data['KC4_TERM_LOC_IND'] > 0 && $data['KC4_TERM_LOC_IND'] < 4) { $termLocFlag = 1; }
                    //subcampo 4
                    if($data['KC4_CRDHLDR_PRESENT_IND'] == 4) { $cardholdrPresFlag = 1; }
                    //subcampo 5
                    if($data['KC4_CRD_PRESENT_IND'] == 1){ $cardPresenceFlag = 1; }
                    //subcampo 6
                    if($data['KC4_CRD_CAPTR_IND'] == 0) { $cardCaptureFlag = 1; }
                    //subcampo 7
                    if($data['KC4_TXN_STAT_IND'] == 0 || $data['KC4_TXN_STAT_IND'] == 4) { $reqStatusFlag = 1; }
                    //subcampo 8
                    if($data['KC4_TXN_SEC_IND'] == 0 || $data['KC4_TXN_SEC_IND'] == 2) { $secLevelFlag = 1; }
                    //subcampo 9
                    if($data['KC4_TXN_RTN_IND'] == 3) { $routingFlag = 1; }
                    //subcampo 10
                    if($data['KC4_CRDHLDR_ACTVT_TERM_IND'] == 0 || $data['KC4_CRDHLDR_ACTVT_TERM_IND'] == 6) { $termActivateFlag = 1; }
                    //subcampo 11
                    switch($data['KC4_TERM_INPUT_CAP_IND']){
                        case 0: $termDataTransFlag = 1; break;
                        case 1: $termDataTransFlag = 1; break;
                        case 6: $termDataTransFlag = 1; break;
                        default: $termDataTransFlag = 0; break;
                    }
                    //subcampo 12
                    switch($data['KC4_CRDHLDR_ID_METHOD']){
                        case ' ': $cardholdrMethodFlag = 1; break;
                        case 0: $cardholdrMethodFlag = 1; break;
                        case 5: $cardholdrMethodFlag = 1; break;
                        case 9: $cardholdrMethodFlag = 1; break;
                        default: $cardholdrMethodFlag = 0; break;
                    }
                    break;
                }
                //validación de transacciones TERMINAL PUNTO DE VENTA
                case '03':{
                    //subcampo 1
                    if($data['KC4_TERM_ATTEND_IND'] == 0) { $termAttFlag = 1; }
                    //subcampo 2
                    if($data['KC4_TERM_OPER_IND'] == 0) { $termOperFlag = 1; }
                    //subcampo 3
                    if($data['KC4_TERM_LOC_IND'] == 0) { $termLocFlag = 1; }
                    //subcampo 4
                    if($data['KC4_CRDHLDR_PRESENT_IND'] == 0) { $cardholdrPresFlag = 1; }
                    //subcampo 5
                    if($data['KC4_CRD_PRESENT_IND'] == 0) { $cardPresenceFlag = 1; } 
                    //subcampo 6
                    if($data['KC4_CRD_CAPTR_IND'] == 0 || $data['KC4_CRD_CAPTR_IND'] == 1) { $cardCaptureFlag = 1; }
                    //subcampo 7
                    if($data['KC4_TXN_STAT_IND'] == 0) { $reqStatusFlag = 1; }
                    //subcampo 8
                    if($data['KC4_TXN_SEC_IND'] == 0 || $data['KC4_TXN_SEC_IND'] == 2) { $secLevelFlag = 1; }
                    //subcampo 9
                    switch($data['KC4_TXN_RTN_IND']){
                        case 0: $routingFlag = 1; break; 
                        case 1: $routingFlag = 1; break;
                        case 3: $routingFlag = 1; break;
                        default: $routingFlag = 0; break;
                    }
                    //subcampo 10
                    switch($data['KC4_CRDHLDR_ACTVT_TERM_IND']){
                        case 0: $termActivateFlag = 1; break;
                        case 7: $termActivateFlag = 1; break;
                        case 9: $termActivateFlag = 1; break;
                        default: $termActivateFlag = 0; break;
                    }
                    //subcampo 11
                    if($data['KC4_TERM_INPUT_CAP_IND'] > 1 && $data['KC4_TERM_INPUT_CAP_IND'] < 10) { $termDataTransFlag = 1; }
                    //subcampo 12
                    switch($data['KC4_CRDHLDR_ID_METHOD']){
                        case ' ': $cardholdrMethodFlag = 1; break;
                        case 0: $cardholdrMethodFlag = 1; break;
                        case 1: $cardholdrMethodFlag = 1; break;
                        case 2: $cardholdrMethodFlag = 1; break;
                        case 5: $cardholdrMethodFlag = 1; break;
                        case 9: $cardholdrMethodFlag = 1; break;
                        default: $cardholdrMethodFlag = 0; break;
                    }
                    break;
                }
                //validación de transacciones COMERCIO INTERRED
                case '04':{
                    //subcampo 1
                    if($data['KC4_TERM_ATTEND_IND'] == 0) { $termAttFlag = 1; }
                    //subcampo 2
                    if($data['KC4_TERM_OPER_IND'] == 0) { $termOperFlag = 1; }
                    //subcampo 3
                    if($data['KC4_TERM_LOC_IND'] == 0) { $termLocFlag = 1; }
                    //subcampo 4
                    if($data['KC4_CRDHLDR_PRESENT_IND'] == 0) { $cardholdrPresFlag = 1; }
                    //subcampo 5
                    if($data['KC4_CRD_PRESENT_IND'] == 0){ $cardPresenceFlag = 1; }
                    //subcampo 6
                    if($data['KC4_CRD_CAPTR_IND'] == 0 || $data['KC4_CRD_CAPTR_IND'] == 1) { $cardCaptureFlag = 1; }
                    //subcampo 7
                    if($data['KC4_TXN_STAT_IND'] == 0) { $reqStatusFlag = 1; }
                    //subcampo 8
                    if($data['KC4_TXN_SEC_IND'] == 0 || $data['KC4_TXN_SEC_IND'] == 2) { $secLevelFlag = 1; }
                    //subcampo 9
                    switch($data['KC4_TXN_RTN_IND']){
                        case 0: $routingFlag = 1; break;
                        case 1: $routingFlag = 1; break;
                        case 3: $routingFlag = 1; break;
                        default: $routingFlag = 0; break;
                    }
                    //subcampo 10
                    switch($data['KC4_CRDHLDR_ACTVT_TERM_IND']){
                        case 0: $termActivateFlag = 1; break;
                        case 1: $termActivateFlag = 1; break;
                        case 9: $termActivateFlag = 1; break;
                        default: $termActivateFlag = 0; break;
                    }
                    //subcampo 11
                    if($data['KC4_TERM_INPUT_CAP_IND'] > 1 && $data['KC4_TERM_INPUT_CAP_IND'] < 10) { $termDataTransFlag = 1; }
                    //subcampo 12
                    switch($data['KC4_CRDHLDR_ID_METHOD']){
                        case ' ': $cardholdrMethodFlag = 1; break;
                        case 0: $cardholdrMethodFlag = 1; break;
                        case 1: $cardholdrMethodFlag = 1; break;
                        case 2: $cardholdrMethodFlag = 1; break;
                        case 5: $cardholdrMethodFlag = 1; break;
                        case 9: $cardholdrPresFlag = 1; break;
                        default: $cardholdrMethodFlag = 0; break;
                    }
                    break;
                }          
                //Validación de TRANSACCIONES MANUALES MOTO.
                case '08':{
                    //subcampo 1
                    if($data['KC4_TERM_ATTEND_IND'] == 2) { $termAttFlag = 1; }
                    //subcampo 2
                    if($data['KC4_TERM_OPER_IND'] == 0) { $termOperFlag = 1; }
                    //subcampo 3
                    if($data['KC4_TERM_LOC_IND'] == 3){ $termLocFlag = 1; } 
                    //subcampo 4 
                    if(strlen($data['KC4_CRDHLDR_PRESENT_IND']) !== 0){
                        switch($data['KC4_CRDHLDR_PRESENT_IND']){
                            case 1: $cardholdrPresFlag = 1; break;
                            case 2: $cardholdrPresFlag= 1; break;
                            case 3: $cardholdrPresFlag = 1; break;
                            default: $cardholdrPresFlag = 0; break;
                        }
                    }
                    //subcampo5
                    if($data['KC4_CRD_PRESENT_IND'] == 1){ $cardPresenceFlag= 1; }
                    //subcampo 6
                    if($data['KC4_CRD_CAPTR_IND'] == 0 || $data['KC4_CRD_CAPTR_IND'] == 1) { $cardCaptureFlag = 1; }
                    //subcampo 7
                    if($data['KC4_TXN_STAT_IND'] == 0) { $reqStatusFlag = 1; }
                    //subcampo 8
                    if($data['KC4_TXN_SEC_IND'] == 0 || $data['KC4_TXN_SEC_IND'] == 2) { $secLevelFlag = 1; }
                    //subcampo 9 
                    if($data['KC4_TXN_RTN_IND'] == 3) { $routingFlag = 1; }
                    //subcampo 10
                    if($data['KC4_CRDHLDR_ACTVT_TERM_IND'] == 0 || $data['KC4_CRDHLDR_ACTVT_TERM_IND'] == 6) { $termActivateFlag = 1; }
                    //subcampo 11
                    if($data['KC4_TERM_INPUT_CAP_IND'] == 1 || $data['KC4_TERM_INPUT_CAP_IND'] == 6) { $termDataTransFlag = 1; }
                    //subcampo 12
                    if($data['KC4_CRDHLDR_ID_METHOD'] == 4){ $cardholdrMethodFlag = 1; }
                    break;
                }
                //Validación de trasacciones COMERCIO ELECTRONICO.
                case '09':{
                    //subcampo 1
                    if($data['KC4_TERM_ATTEND_IND'] == 1) { $termAttFlag = 1; }
                    //subcampo 2
                    if($data['KC4_TERM_OPER_IND'] == 0) { $termOperFlag = 1; }
                    //subcampo 3
                    if($data['KC4_TERM_LOC_IND'] == 2) { $termLocFlag = 1; }
                    //subcampo 4
                    if($data['KC4_CRDHLDR_PRESENT_IND'] == 5) { $cardholdrPresFlag = 1; }
                    //subcampo 5
                    if($data['KC4_CRD_PRESENT_IND'] == 1){ $cardPresenceFlag = 1; }
                    //subcampo 6
                    if($data['KC4_CRD_CAPTR_IND'] == 0) { $cardCaptureFlag = 1; }
                    //subcampo 7
                    if($data['KC4_TXN_STAT_IND'] == 0) { $reqStatusFlag = 1; }
                    //subcampo 8
                    if($data['KC4_TXN_SEC_IND'] == 0 || $data['KC4_TXN_SEC_IND'] == 2) { $secLevelFlag = 1; }
                    //subcampo 9 
                    if($data['KC4_TXN_RTN_IND'] == 3) { $routingFlag = 1; }
                    //subcampo 10
                    if($data['KC4_CRDHLDR_ACTVT_TERM_IND'] == 6){ $termActivateFlag = 1; }
                    //subcampo 11
                    switch($data['KC4_TERM_INPUT_CAP_IND']){
                        case 0: $termDataTransFlag = 1; break;
                        case 1: $termDataTransFlag = 1; break;
                        case 6: $termDataTransFlag = 1; break;
                        default: $termDataTransFlag = 0; break;
                    }
                    //subcampo 12
                    switch($data['KC4_CRDHLDR_ID_METHOD']){
                        case ' ': $cardholdrMethodFlag = 1; break;
                        case 0: $cardholdrMethodFlag = 1; break;
                        case 3: $cardholdrMethodFlag = 1; break;
                        case 4: $cardholdrMethodFlag = 1; break;
                        case 9: $cardholdrMethodFlag = 1; break;
                    }
                    break;
                }
                //validación de transacciones SERVIDORES MULTICAJA (AUDIORESPYESTA IVR)
                case '14': {
                    //subcampo 1
                    if($data['KC4_TERM_ATTEND_IND'] == 1) { $termAttFlag = 1; }
                    //subcampo 2
                    if($data['KC4_TERM_OPER_IND'] == 0){ $termOperFlag = 1; }
                    //subcampo 3
                    if($data['KC4_TERM_LOC_IND'] == 2) { $termLocFlag = 1; }
                    //subcampo 4
                    if($data['KC4_CRDHLDR_PRESENT_IND'] == 5) { $cardholdrPresFlag = 1; }
                    //subcampo 5
                    if($data['KC4_CRD_PRESENT_IND'] == 1) { $cardPresenceFlag = 1; }
                    //subcampo 6
                    if($data['KC4_CRD_CAPTR_IND'] == 0) { $cardCaptureFlag = 1; }
                    //subcampo 7
                    if($data['KC4_TXN_STAT_IND'] == 0) { $reqStatusFlag = 1; }
                    //subcampo 8
                    if($data['KC4_TXN_SEC_IND'] == 0 || $data['KC4_TXN_SEC_IND']) { $secLevelFlag = 1; }
                    //subcampo 9
                    if($data['KC4_TXN_RTN_IND'] == 3) { $routingFlag = 1; }
                    //subcampo 10
                    if($data['KC4_CRDHLDR_ACTVT_TERM_IND'] == 6) { $termActivateFlag = 1; }
                    //subampo 11
                    if($data['KC4_TERM_INPUT_CAP_IND'] == 0 || $data['KC4_TERM_INPUT_CAP_IND'] == 6) { $termDataTransFlag = 1; }
                    //subcampo 12 
                    switch($data['KC4_CRDHLDR_ID_METHOD']){
                        case ' ': $cardholdrMethodFlag = 1; break;
                        case 0: $cardholdrMethodFlag = 1; break;
                        case 9: $cardholdrMethodFlag = 1; break;
                        default: $cardholdrMethodFlag = 1; break;
                    }
                    break;
                }
                //validación de transacciones COMERCIOS MULTICAJA
                case '17':{
                    //subcampo 1
                    if($data['KC4_TERM_ATTEND_IND'] == 0) { $termAttFlag = 1; }
                    //subcampo 2
                    if($data['KC4_TERM_OPER_IND'] == 0){ $termOperFlag = 1; }
                    //subcampo 3
                    if($data['KC4_TERM_LOC_IND'] == 0) { $termLocFlag = 1; }
                    //subcampo 4
                    if($data['KC4_CRDHLDR_PRESENT_IND'] == 0) { $cardholdrPresFlag = 1; }
                    //subcampo 5
                    if($data['KC4_CRD_PRESENT_IND'] == 0) { $cardPresenceFlag = 1; }
                    //subcampo 6
                    if($data['KC4_CRD_CAPTR_IND'] == 0) { $cardCaptureFlag = 1; }
                    //subcampo 7
                    if($data['KC4_TXN_STAT_IND'] == 0) { $reqStatusFlag = 1; }
                    //subcampo 8
                    if($data['KC4_TXN_SEC_IND'] == 0 || $data['KC4_TXN_SEC_IND'] == 2) { $secLevelFlag = 1; }
                    //subcampo 9
                    switch($data['KC4_TXN_RTN_IND']){
                        case 0: $routingFlag = 1; break;
                        case 1: $routingFlag = 1; break;
                        case 3: $routingFlag = 1; break;
                        default: $routingFlag = 0; break;
                    }
                    //subcampo 10
                    if($data['KC4_CRDHLDR_ACTVT_TERM_IND'] ==  0) { $termActivateFlag = 1; }
                    //subcampo 11
                    if($data['KC4_TERM_INPUT_CAP_IND'] > 1 && $data['KC4_TERM_INPUT_CAP_IND'] < 10) { $termDataTransFlag = 1; }
                    //subcampo 12
                    switch($data['KC4_CRDHLDR_ID_METHOD']){
                        case 1: $cardholdrMethodFlag = 1;  break;
                        case 2: $cardholdrMethodFlag = 1; break;
                        case 5: $cardholdrMethodFlag = 1; break;
                        default: $cardholdrMethodFlag = 0; break;
                    }
                }
                //validación de transacciones ACTIVADAS POR EL TARJETAHABIENTE
                case '19':{
                    //subcampo 1
                    if($data['KC4_TERM_ATTEND_IND'] == 1) { $termAttFlag = 1; }
                    //subcampo 4
                    if($data['KC4_CRDHLDR_PRESENT_IND'] == 0) { $cardholdrPresFlag = 1; }
                    //subcampo 5
                    if($data['KC4_CRD_PRESENT_IND'] == 0) { $cardPresenceFlag = 1; }
                    //subcampo 10
                    if($data['KC4_CRDHLDR_ACTVT_TERM_IND'] > -1 && $data['KC4_CRDHLDR_ACTVT_TERM_IND'] < 4) { $termActivateFlag = 1; }
                    //subcampo 12
                    if($data['KC4_CRDHLDR_ID_METHOD'] > 1 && $data['KC4_CRDHLDR_ID_METHOD'] < 4) { $cardholdrMethodFlag = 1; }
                    break;
                }
            }

            $answer[$key] = new stdClass();
            $answer[$key]->ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key]->ID_Code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key]->ID_Entry_Mode = $data['ENTRY_MODE'];
            $answer[$key]->ID_Terminal_Attended = $data['KC4_TERM_ATTEND_IND']; //subcampo 1
            $answer[$key]->TermAttFlag = $termAttFlag;
            $answer[$key]->ID_Terminal = $data['KC4_TERM_OPER_IND']; //subcampo 2
            $answer[$key]->TermOperFlag = $termOperFlag;
            $answer[$key]->Terminal_Location = $data['KC4_TERM_LOC_IND']; //subcampo 3
            $answer[$key]->TermLocFlag = $termLocFlag;
            $answer[$key]->ID_Cardholder_Presence = $data['KC4_CRDHLDR_PRESENT_IND']; //subcampo 4
            $answer[$key]->CardholdrPresFlag = $cardholdrPresFlag;
            $answer[$key]->ID_Card_Presence = $data['KC4_CRD_PRESENT_IND']; //subcampo 5
            $answer[$key]->CardpresenceFlag = $cardPresenceFlag;
            $answer[$key]->ID_Card_Capture = $data['KC4_CRD_CAPTR_IND']; //subcampo 6
            $answer[$key]->CardCaptureFlag = $cardCaptureFlag;
            $answer[$key]->ID_Status = $data['KC4_TXN_STAT_IND']; //subcampo 7
            $answer[$key]->ReqStatusFlag = $reqStatusFlag;
            $answer[$key]->Security_Level = $data['KC4_TXN_SEC_IND']; //subcampo 8
            $answer[$key]->SecLevelFlag = $secLevelFlag;
            $answer[$key]->Routing_Indicator = $data['KC4_TXN_RTN_IND']; //subcampo 9
            $answer[$key]->routingFlag = $routingFlag;
            $answer[$key]->Terminal_Activation_Cardholder = $data['KC4_CRDHLDR_ACTVT_TERM_IND']; //subcampo 10
            $answer[$key]->TermActivationFlag = $termActivateFlag;
            $answer[$key]->ID_Terminal_Data_Transfer = $data['KC4_TERM_INPUT_CAP_IND']; //subcampo 11
            $answer[$key]->TermDataTransFlag = $termDataTransFlag; 
            $answer[$key]->ID_Cardholder_Method = $data['KC4_CRDHLDR_ID_METHOD']; // subcampo 12
            $answer[$key]->CardholdrMethodFlag = $cardholdrMethodFlag;
        }

        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }

    //FUNCIÓN PARA MANDAR INFORMACIÓN DE LA TABLA DE COMERCIOS (FILTRADA)
    public function getDataTableComerceFilter(Request $request)
    {
        $values = array();
        $label = [
            'KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'KC4_TERM_ATTEND_IND', 'KC4_TERM_OPER_IND', 'KC4_TERM_LOC_IND', 'KC4_CRDHLDR_PRESENT_IND',
            'KC4_CRD_PRESENT_IND', 'KC4_CRD_CAPTR_IND', 'KC4_TXN_STAT_IND', 'KC4_TXN_SEC_IND', 'KC4_TXN_RTN_IND',
            'KC4_CRDHLDR_ACTVT_TERM_IND', 'KC4_TERM_INPUT_CAP_IND', 'KC4_CRDHLDR_ID_METHOD'
        ];

        //No se usa estructura de control por el request
        $values[0] = $request->Kq2;
        $values[1] = $request->Code_Response;
        $values[2] = $request->Entry_Mode;
        $values[3] = $request->ID_Terminal_Attended;
        $values[4] = $request->ID_Terminal;
        $values[5] = $request->Terminal_Location;
        $values[6] = $request->ID_Cardholder_Presence;
        $values[7] = $request->ID_Card_Presence;
        $values[8] = $request->ID_Card_Capture;
        $values[9] = $request->ID_Status;
        $values[10] = $request->Security_Level;
        $values[11] = $request->Routing_Indicator;
        $values[12] = $request->Terminal_Activation_Cardholder;
        $values[13] = $request->ID_Terminal_Data_Transfer;
        $values[14] = $request->ID_Cardholder_Method;

        $answer = array();
        $array = array();
        $response = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, FIID_TARJ,FIID_COMER, NOMBRE_DE_TERMINAL,
        R,NUM_SEC,MONTO1 from test where ";

        //Eliminar aquellos elementos que esten vacios para hacer la consulta
        for ($key = 0; $key < 15; $key++) {
            if ($values[$key] == "NonValue" || $values[$key] == "allData") {
                unset($values[$key]);
                unset($label[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($label);

        switch (sizeof($filteredValues)) {
            case 1: {
                $response = DB::select($query . $filteredLabels[0] . " = ?", [$filteredValues[0]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 2: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? )",
                [$filteredValues[0], $filteredValues[1]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 3: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 4: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 5: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? )",
                [ $filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3],
                $filteredValues[4]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 6: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? )and
                (".$filteredLabels[1]." = ? )and 
                (".$filteredLabels[2]." = ? )and 
                (".$filteredLabels[3]." = ? )and 
                (".$filteredLabels[4]." = ? )and
                (".$filteredLabels[5]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], 
                $filteredValues[4], $filteredValues[5]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 7: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 8: {
                    $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 9: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and 
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and 
                (".$filteredLabels[3]." = ? ) and 
                (".$filteredLabels[4]." = ? ) and 
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 10: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 11: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? ) and
                (".$filteredLabels[10]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 12: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? ) and
                (".$filteredLabels[10]." = ? ) and
                (".$filteredLabels[11]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10], $filteredValues[11]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 13: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? ) and
                (".$filteredLabels[10]." = ? ) and
                (".$filteredLabels[11]." = ? ) and
                (".$filteredLabels[12]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10], $filteredValues[11], $filteredValues[12]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 14: {
                    $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? ) and
                (".$filteredLabels[10]." = ? ) and
                (".$filteredLabels[11]." = ? ) and
                (".$filteredLabels[12]." = ? ) and
                (".$filteredLabels[13]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10], $filteredValues[11], $filteredValues[12], $filteredValues[13]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 15: {
                $response = DB::select($query."
                (".$filteredLabels[0]." = ? ) and
                (".$filteredLabels[1]." = ? ) and
                (".$filteredLabels[2]." = ? ) and
                (".$filteredLabels[3]." = ? ) and
                (".$filteredLabels[4]." = ? ) and
                (".$filteredLabels[5]." = ? ) and
                (".$filteredLabels[6]." = ? ) and
                (".$filteredLabels[7]." = ? ) and
                (".$filteredLabels[8]." = ? ) and
                (".$filteredLabels[9]." = ? ) and
                (".$filteredLabels[10]." = ? ) and
                (".$filteredLabels[11]." = ? ) and
                (".$filteredLabels[12]." = ? ) and
                (".$filteredLabels[13]." = ? ) and
                (".$filteredLabels[14]." = ? )",
                [$filteredValues[0], $filteredValues[1], $filteredValues[2], $filteredValues[3], $filteredValues[4],
                $filteredValues[5], $filteredValues[6], $filteredValues[7], $filteredValues[8], $filteredValues[9],
                $filteredValues[10], $filteredValues[11], $filteredValues[12], $filteredValues[13], $filteredValues[14]]);
                $array = json_decode(json_encode($response), true);
                break;
            }
            default: {
                $response = DB::select("select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, FIID_TARJ,FIID_COMER,NOMBRE_DE_TERMINAL,
                R,NUM_SEC,MONTO1 from test");
                $array = json_decode(json_encode($response), true);
                break;
            }
        }

        foreach ($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key]->ID_Code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key]->ID_Entry_Mode = $data['ENTRY_MODE'];
            $answer[$key]->Fiid_Card = $data['FIID_TARJ'];
            $answer[$key]->Fiid_Comerce = $data['FIID_COMER'];
            $answer[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $answer[$key]->R = $data['R'];
            $answer[$key]->Number_Sec = $data['NUM_SEC'];
            $answer[$key]->amount = number_format($data['MONTO1'], 2, '.');
        }

        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }
}
