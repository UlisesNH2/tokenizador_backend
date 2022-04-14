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

            if($data['KB4_PT_SRV_ENTRY_MDE'] == ' '){
                $answer[$key] = new stdClass();
                $answer[$key] -> Service_EntryMode = $data['KB4_PT_SRV_ENTRY_MDE'];
                $answer[$key] -> serviceEMFlag = 0;
                $answer[$key] -> Capacity_Terminal = $data['KB4_TERM_ENTRY_CAP'];
                $answer[$key] -> capTermFlag = 0;
                $answer[$key] -> EVM_Status = $data['KB4_LAST_EMV_STAT'];
                $answer[$key] -> evmStatFlag = 0;
                $answer[$key] -> Data_Suspect = $data['KB4_DATA_SUSPECT'];
                $answer[$key] -> dataSuspFlag = 0;
                $answer[$key] -> PAN_Number = $data['KB4_APPL_PAN_SEQ_NUM'];
                $answer[$key] -> panFlag = 0;
                $answer[$key] -> Device_Info = $data['KB4_DEV_INFO'];
                $answer[$key] -> devinfoFlag = 0; 
                $answer[$key] -> Online_Code = $data['KB4_RSN_ONL_CDE'];
                $answer[$key] -> onlCodeFlag = 0;
                $answer[$key] -> ARQC_Verification = $data['KB4_ARQC_VRFY'];
                $answer[$key] -> arqcVerFlag = 0;
                $answer[$key] -> ID_Response_ISO = $data['KB4_ISO_RC_IND'];
                $answer[$key] -> IDrespFlag = 0;
            }
        }

        foreach($array as $key => $data){
            $serviceEMFlag = 0; $capTermFlag = 0; $evmStatFlag = 0;
            $dataSusFlag = 0; $panFlag = 0; $devinfoFlag = 0;
            $onlCodeflag = 0; $arqcVerFlag = 0; $IDrespFlag = 0;
            if(strlen($data['KB4_PT_SRV_ENTRY_MDE']) == 3){
                $serviceEMFlag = 1;

                if(strlen($data['KB4_TERM_ENTRY_CAP']) == 1){
                    switch($data['KB4_TERM_ENTRY_CAP']){
                        case 0: $capTermFlag = 1; break;
                        case 2: $capTermFlag = 1; break;
                        case 5: $capTermFlag = 1; break;
                        default: $capTermFlag = 0;
                    }
                }else{ $capTermFlag = 0; }

                if(strlen($data['KB4_LAST_EMV_STAT']) == 1){
                    switch($data['KB4_LAST_EMV_STAT']){
                        case 0: $evmStatFlag = 1; break;
                        case 1: $evmStatFlag = 1; break;
                        case '': $evmStatFlag = 1; break;
                        default: $evmStatFlag = 0; 
                    }
                }else{ $evmStatFlag = 0; }

                if(strlen($data['KB4_DATA_SUSPECT']) == 1){
                    switch($data['KB4_DATA_SUSPECT']){
                        case 0: $dataSusFlag = 1; break;
                        case 1: $dataSusFlag = 1; break;
                        case '': $dataSusFlag = 1; break;
                        default: $dataSusFlag = 0;
                    }
                }else{ $dataSusFlag = 0; }

                if(strlen($data['KB4_APPL_PAN_SEQ_NUM']) == 2){ $panFlag = 1; }
                if(strlen($data['KB4_DEV_INFO']) == 6){ $devinfoFlag = 1; }
                if(strlen($data['KB4_RSN_ONL_CDE']) == 4){ $onlCodeflag = 1; }

                if(strlen($data['KB4_ARQC_VRFY']) == 1 || strlen($data['KB4_ARQC_VRFY']) == 0){
                    switch($data['KB4_ARQC_VRFY']){
                        case 0: $arqcVerFlag = 1; break;
                        case 1: $arqcVerFlag = 1; break;
                        case 2: $arqcVerFlag = 1; break; 
                        case 3: $arqcVerFlag = 1; break;
                        case 4: $arqcVerFlag = 1; break;
                        case 9: $arqcVerFlag = 1; break;
                        default: $arqcVerFlag = 0;
                    }
                }else{ $arqcVerFlag = 0; }

                if(strlen($data['KB4_ISO_RC_IND']) == 1 || strlen($data['KB4_ISO_RC_IND']) == 0){
                    switch($data['KB4_ISO_RC_IND']){
                        case 0: $IDrespFlag = 1; break;
                        case 1: $IDrespFlag = 1; break;
                        case '': $IDrespFlag = 1; break;
                        default: $IDrespFlag = 0;
                    }
                }else { $IDrespFlag = 0; }

                $answer[$key] = new stdClass();
                $answer[$key] -> Service_EntryMode = $data['KB4_PT_SRV_ENTRY_MDE'];
                $answer[$key] -> serviceEMFlag = $serviceEMFlag;
                $answer[$key] -> Capacity_Terminal = $data['KB4_TERM_ENTRY_CAP'];
                $answer[$key] -> capTermFlag = $capTermFlag;
                $answer[$key] -> EVM_Status = $data['KB4_LAST_EMV_STAT'];
                $answer[$key] -> evmStatFlag = $evmStatFlag;
                $answer[$key] -> Data_Suspect = $data['KB4_DATA_SUSPECT'];
                $answer[$key] -> dataSuspFlag = $dataSusFlag;
                $answer[$key] -> PAN_Number = $data['KB4_APPL_PAN_SEQ_NUM'];
                $answer[$key] -> panFlag = $panFlag;
                $answer[$key] -> Device_Info = $data['KB4_DEV_INFO'];
                $answer[$key] -> devinfoFlag = $devinfoFlag; 
                $answer[$key] -> Online_Code = $data['KB4_RSN_ONL_CDE'];
                $answer[$key] -> onlCodeFlag = $onlCodeflag;
                $answer[$key] -> ARQC_Verification = $data['KB4_ARQC_VRFY'];
                $answer[$key] -> arqcVerFlag = $arqcVerFlag;
                $answer[$key] -> ID_Response_ISO = $data['KB4_ISO_RC_IND'];
                $answer[$key] -> IDrespFlag = $IDrespFlag;
            }
        }
        
        $arrayJSON = json_decode(json_encode($answer), true);
        $arrayJSONOrdered = array_values($arrayJSON);
        return $arrayJSONOrdered;
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
