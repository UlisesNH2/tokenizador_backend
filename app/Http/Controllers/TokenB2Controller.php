<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class TokenB2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $tokenb2 = DB::select("select KB2_BIT_MAP,KB2_USR_FLD1,KB2_ARQC,KB2_AMT_AUTH,KB2_AMT_OTHER,
        KB2_ATC,KB2_TERM_CTRY_CDE,KB2_TRAN_CRNCY_CDE,KB2_TRAN_DAT,KB2_TRAN_TYPE,
        KB2_UNPREDICT_NUM,KB2_ISS_APPL_DATA_LGTH,KB2_ISS_APPL_DATA,
        KB2_CRYPTO_INFO_DATA,KB2_TVR,KB2_AIP from test");
        $array = json_decode(json_encode($tokenb2), true);

        $answer = array();

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> Bit_Map = $data['KB2_BIT_MAP'];
            $answer[$key] -> User_Field_One = $data['KB2_USR_FLD1'];
            $answer[$key] -> ARQC = $data['KB2_ARQC'];
            $answer[$key] -> AMT_Auth = $data['KB2_AMT_AUTH'];
            $answer[$key] -> AMT_Other = $data['KB2_AMT_OTHER'];
            $answer[$key] -> ATC = $data['KB2_ATC'];
            $answer[$key] -> Terminal_Country_Code = $data['KB2_TERM_CTRY_CDE'];
            $answer[$key] -> Terminal_Currency_Code = $data['KB2_TRAN_CRNCY_CDE'];
            $answer[$key] -> Transaction_Date = $data['KB2_TRAN_DAT'];
            $answer[$key] -> Transaction_Type = $data['KB2_TRAN_TYPE'];
            $answer[$key] -> Umpedict_Number = $data['KB2_UNPREDICT_NUM'];
            $answer[$key] -> Issuing_App_Data_Length = $data['KB2_ISS_APPL_DATA_LGTH'];
            $answer[$key] -> Issuing_App_Data = $data['KB2_ISS_APPL_DATA'];
            $answer[$key] -> TVR = $data['KB2_TVR'];
            $answer[$key] -> AIP = $data['KB2_AIP'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }

    public function getDataTableFilter(Request $request){
        $values = array();
        $label = ['KB2_BIT_MAP', 'KB2_USR_FLD1', 'KB2_ARQC', 'KB2_AMT_AUTH', 'KB2_AMT_OTHER', 'KB2_ATC',
        'KB2_TERM_CTRY_CDE', 'KB2_TRAN_CRNCY_CDE', 'KB2_TRAN_DAT', 'KB2_TRAN_TYPE', 'KB2_UNPREDICT_NUM',
        'KB2_ISS_APPL_DATA_LGTH', 'KB2_ISS_APPL_DATA', 'KB2_TVR', 'KB2_AIP'];

        $values[0] = $request -> Bit_Map;
        $values[1] = $request -> User_Field_One;
        $values[2] = $request -> ARQC;
        $values[3] = $request -> AMT_Auth;
        $values[4] = $request -> AMT_Other;
        $values[5] = $request -> ATC;
        $values[6] = $request -> Terminal_Country_Code;
        $values[7] = $request -> Terminal_Currency_Code;
        $values[8] = $request -> Transaction_Date;
        $values[9] = $request -> Transaction_Type;
        $values[10] = $request -> Umpedict_Number;
        $values[11] = $request -> Issuing_App_Data_Length;
        $values[12] = $request -> Issuing_App_Data;
        $values[13] = $request -> TVR;
        $values[14] = $request -> AIP;

        $answer = array();

        for($key = 0; $key < 15; $key++){
            if($values[$key] == "NonValue"){
                unset($values[$key]);
                unset($label[$key]);
            }
        };
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
        $arrayJSONOrdened = array_values($arrayJSON);
        return $arrayJSONOrdened;
    }
}
