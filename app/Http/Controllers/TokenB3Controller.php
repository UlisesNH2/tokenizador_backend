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
}
