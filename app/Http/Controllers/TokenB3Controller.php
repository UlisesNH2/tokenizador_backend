<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class TokenB3Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tokenb3 = DB::select("select KB3_BIT_MAP, KB3_TERM_SRL_NUM, KB3_EMV_TERM_CAP, KB3_USR_FLD1, 
        KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME  
        from test ");
        $array = json_decode(json_encode($tokenb3), true);

        $answer = array();

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> Bit_Map = $data['KB3_BIT_MAP'];
            $answer[$key] -> Terminal_Serial_Number = $data['KB3_TERM_SRL_NUM'];
            $answer[$key] -> Check_Cardholder = $data['KB3_EMV_TERM_CAP'];
            $answer[$key] -> User_Field_One = $data['KB3_USR_FLD1'];
            $answer[$key] -> User_Field_Two = $data['KB3_USR_FLD2'];
            $answer[$key] -> Terminal_Type_EMV = $data['KB3_EMV_TERM_TYPE'];
            $answer[$key] -> App_Version_Number = $data['KB3_APP_VER_NUM'];
            $answer[$key] -> CVM_Result = $data['KB3_CVM_RSLTS'];
            $answer[$key] -> File_Name_Length = $data['KB3_DF_NAME_LGTH'];
            $answer[$key] -> File_Name = $data['KB3_DF_NAME'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }

    public function getDataTableFilter(Request $request){
        $values = array();
        $label = ['KB3_BIT_MAP', 'KB3_TERM_SRL_NUM', 'KB3_EMV_TERM_CAP', 'KB3_USR_FLD1', 'KB3_USR_FLD2',
                'KB3_EMV_TERM_TYPE', 'KB3_APP_VER_NUM', 'KB3_CVM_RSLTS', 'KB3_DF_NAME_LGTH', 'KB3_DF_NAME'];
        
        $values[0] = $request -> Bit_Map;
        $values[1] = $request -> Terminal_Serial_Number;
        $values[2] = $request -> Check_Cardholder;
        $values[3] = $request -> User_Field_One;
        $values[4] = $request -> User_Field_Two;
        $values[5] = $request -> Terminal_Type_EMV;
        $values[6] = $request -> App_Version_Number;
        $values[7] = $request -> CVM_Result;
        $values[8] = $request -> File_Name_Length;
        $values[9] = $request -> File_Name;

        $answer = array();

        for($key = 0; $key < 10; $key++){
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
