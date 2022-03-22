<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class TokenB4Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tokenB4 = DB::select("select KB4_PT_SRV_ENTRY_MDE, KB4_TERM_ENTRY_CAP, KB4_LAST_EMV_STAT, KB4_DATA_SUSPECT,
        KB4_APPL_PAN_SEQ_NUM, KB4_DEV_INFO, KB4_RSN_ONL_CDE, KB4_ARQC_VRFY, KB4_ISO_RC_IND  from test");
        $array = json_decode(json_encode($tokenB4), true); //Array asociativo

        $answer = array();

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> Service_EntryMode = $data['KB4_PT_SRV_ENTRY_MDE'];
            $answer[$key] -> Capacity_Terminal = $data['KB4_TERM_ENTRY_CAP'];
            $answer[$key] -> EVM_Status = $data['KB4_LAST_EMV_STAT'];
            $answer[$key] -> Data_Suspect = $data['KB4_DATA_SUSPECT'];
            $answer[$key] -> PAN_Number = $data['KB4_APPL_PAN_SEQ_NUM'];
            $answer[$key] -> Device_Info= $data['KB4_DEV_INFO'];
            $answer[$key] -> Online_Code = $data['KB4_RSN_ONL_CDE'];
            $answer[$key] -> ARQC_Verification = $data['KB4_ARQC_VRFY'];
            $answer[$key] -> ID_Response_ISO= $data['KB4_ISO_RC_IND'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }

    public function getDataTableFilter(Request $request){
        $values = array();
        $labels = ['KB4_PT_SRV_ENTRY_MDE', 'KB4_TERM_ENTRY_CAP', 'KB4_LAST_EMV_STAT', 'KB4_DATA_SUSPECT', 
        'KB4_APPL_PAN_SEQ_NUM', 'KB4_DEV_INFO', 'KB4_RSN_ONL_CDE', 'KB4_ARQC_VRFY', 'KB4_ISO_RC_IND'];

        $values[0] = $request -> Service_EntryMode;
        $values[1] = $request -> Capacity_Terminal;
        $values[2] = $request -> EVM_Status;
        $values[3] = $request -> Data_Suspect;
        $values[4] = $request -> PAN_Number;
        $values[5] = $request -> Device_Info;
        $values[6] = $request -> Online_Code;
        $values[7] = $request -> ARQC_Verification;
        $values[8] = $request -> ID_Response_ISO;

        $answer = array();

        for($key = 0; $key < 9; $key++){
            if($values[$key] == "NonValue"){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }

        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);

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
        $arrayJSONOrdened = array_values($arrayJSON);
        return $arrayJSONOrdened;
    }
}
