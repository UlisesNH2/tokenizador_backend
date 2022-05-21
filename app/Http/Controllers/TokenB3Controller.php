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
    public function index(Request $request)
    {
        $kq2 = $request -> Kq2;
        $codeResponse = $request -> Code_Response;
        $entryMode = $request -> Entry_Mode;
        $numberFilters = 0;
        $flagKq2 = false;
        $flagCode = false;
        $flagEntry = false;
        $response = array();
        $query = "select KB3_BIT_MAP, KB3_TERM_SRL_NUM, KB3_EMV_TERM_CAP, KB3_USR_FLD1, 
        KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME  
        from test where ";

        //Detectar cuales filtros son los que están siendo utilizados
        if(!empty($kq2)){ $numberFilters++; $flagKq2 = true; }
        if(!empty($codeResponse)){ $numberFilters++; $flagCode = true; }
        if(!empty($entryMode)){ $numberFilters++; $flagEntry = true; }

        switch($numberFilters){
            case 1:{ //Un solo filtro elegido
                if($flagKq2){
                    for($i = 0; $i < count($kq2); $i++){
                        $response = array_merge($response, DB::select($query.
                        "KQ2_ID_MEDIO_ACCESO = ?", [$kq2[$i]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                if($flagCode){
                    for($i = 0; $i < count($codeResponse); $i++){
                        $response = array_merge($response, DB::select($query.
                        "CODIGO_RESPUESTA = ?", [$codeResponse[$i]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                if($flagEntry){
                    for($i = 0; $i < count($entryMode); $i++){
                        $response = array_merge($response, DB::select($query.
                        "ENTRY_MODE = ?", [$entryMode[$i]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                break;
            }
            case 2: { //Dos filtros elegidos
                if($flagKq2){//Medio Acceso
                    if($flagCode && !$flagEntry){
                        $firstLength = max($kq2, $codeResponse);
                        switch($firstLength){
                            case $kq2: {
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?", [$kq2[$i], $codeResponse[$j]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                            case $codeResponse: {
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0; $j < count($kq2); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? ",[$kq2[$j], $codeResponse[$i]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                        }
                    }else{
                        if(!$flagCode && $flagEntry){
                            $firstLength = max($kq2, $entryMode);
                            switch($firstLength){
                                case $kq2: {
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?", [$kq2[$i], $entryMode[$j]]));
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $entryMode: {
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?", [$kq2[$j], $entryMode[$i]]));
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                            }
                        }
                    }
                }else{
                    if($flagCode && $flagEntry){
                        $firstLength = max($codeResponse, $entryMode);
                        switch($firstLength){
                            case $codeResponse: {
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0; $j < count($entryMode); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", [$codeResponse[$i], $entryMode[$j]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                            case $entryMode: {
                                for($i = 0; $i < count($entryMode); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", [$codeResponse[$j], $entryMode[$i]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                        }
                    }
                }
                break;
            }
            case 3: { //Tres filtros elegidos
                if($flagKq2 && $flagCode && $flagEntry){
                    $firstLength = max($kq2, $codeResponse, $entryMode);
                    switch($firstLength){
                        case $kq2:{
                            $secondLength = max($codeResponse, $entryMode);
                            switch($secondLength){
                                case $codeResponse: {
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($codeResponse); $j++){
                                            for($z = 0; $z < count($entryMode); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$i], $codeResponse[$j], $entryMode[$z]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $entryMode: {
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            for($z = 0; $z < count($codeResponse); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$i], $codeResponse[$z], $entryMode[$j]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                            }
                            break;
                        }
                        case $codeResponse: {
                            $secondLength = max($kq2, $entryMode);
                            switch($secondLength){
                                case $kq2:{
                                    for($i = 0; $i < count($codeResponse); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            for($z = 0; $z < count($entryMode); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$j], $codeResponse[$i], $entryMode[$z]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $entryMode: {
                                    for($i = 0; $i < count($codeResponse); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            for($z = 0; $z < count($kq2); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$z], $codeResponse[$i], $entryMode[$j]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                            }
                            break;
                        }
                        case $entryMode:{
                            $secondLength = max($kq2, $codeResponse);
                            switch($secondLength){
                                case $kq2:{
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            for($z = 0; $z < count($codeResponse); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$j], $codeResponse[$z], $entryMode[$i]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $codeResponse: {
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($codeResponse); $j++){
                                            for($z = 0; $z < count($kq2); $z++){
                                                $response = array_merge($response, DB::select($query.
                                                "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$z], $codeResponse[$j], $entryMode[$i]]));
                                            }
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
                break;
            }
            default:{
                $response = DB::select("select KB3_BIT_MAP, KB3_TERM_SRL_NUM, KB3_EMV_TERM_CAP, KB3_USR_FLD1, 
                KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME  
                from test");
                $array = json_decode(json_encode($response), true);
                break;
            }
        }

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
        $label = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'KB3_BIT_MAP', 'KB3_TERM_SRL_NUM', 'KB3_EMV_TERM_CAP', 'KB3_USR_FLD1', 'KB3_USR_FLD2',
                'KB3_EMV_TERM_TYPE', 'KB3_APP_VER_NUM', 'KB3_CVM_RSLTS', 'KB3_DF_NAME_LGTH', 'KB3_DF_NAME'];
        $values[0] = $request -> Kq2;
        $values[1] = $request -> Code_Response;
        $values[2] = $request -> Entry_Mode;
        $values[3] = $request -> Bit_Map;
        $values[4] = $request -> Terminal_Serial_Number;
        $values[5] = $request -> Check_Cardholder;
        $values[6] = $request -> User_Field_One;
        $values[7] = $request -> User_Field_Two;
        $values[8] = $request -> Terminal_Type_EMV;
        $values[9] = $request -> App_Version_Number;
        $values[10] = $request -> CVM_Result;
        $values[11] = $request -> File_Name_Length;
        $values[12] = $request -> File_Name;

        $response = array();
        $answer = array();
        $arrayValues = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB3_BIT_MAP, KB3_TERM_SRL_NUM, KB3_EMV_TERM_CAP, KB3_USR_FLD1, KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, 
        KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME, FIID_TARJ,FIID_COMER,NOMBRE_DE_TERMINAL,CODIGO_RESPUESTA,R,NUM_SEC from test where ";

        $queryOutFilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB3_BIT_MAP, KB3_TERM_SRL_NUM, KB3_EMV_TERM_CAP, KB3_USR_FLD1, KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, 
        KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME, FIID_TARJ,FIID_COMER,NOMBRE_DE_TERMINAL,CODIGO_RESPUESTA,R,NUM_SEC from test";

        //Detectar cuales son los filtros utilizados
        for($key = 0; $key < 13; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($label[$key]);
            }
        }

        $filteredValues = array_values($values);
        $filteredLabels = array_values($label);

        if(empty($filteredValues)){ //En caso de que no se utilicen los filtros
            $response = DB::select($queryOutFilters);
            $array = json_decode(json_encode($response), true);
        }else{
            if(count($filteredValues) <= 1){ //Solo se utiliza un filtro
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        $response = array_merge($response, DB::select($query.$filteredLabels[$i]."= ?", 
                        [$filteredValues[$i][$j]]));
                    }
                }
                $array = json_decode(json_encode($response), true);
            }else{ //En caso de existir más valores dentro del arreglo de $filteredValues
                //Ingresar todos los $filteredValues en un solo arreglo
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        array_push($arrayValues, $filteredValues[$i][$j]);
                    }
                }
                $z = 1; //variable para el control de la longitud del query
                //Construcción del query varias consultas al mismo tiempo
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        if($j == count($filteredValues[$i]) -1){
                            if($j == 0){
                                if($z == count($arrayValues)){
                                    $query .= "(".$filteredLabels[$i]." = ?)";
                                }else{
                                    $query .= "(".$filteredLabels[$i]." = ?) and ";
                                }
                                $z++;
                            }else{
                                if($z == count($arrayValues)){
                                    $query .= $filteredLabels[$i]." = ?)";
                                    $z = 1;
                                }else{
                                    $query .= $filteredLabels[$i]." = ?) and ";
                                    $z++;
                                }
                            }
                        }else{
                            if($j == 0){
                                $query .= "(".$filteredLabels[$i]." = ? or ";
                                $z++;
                            }else{
                                $query .= $filteredLabels[$i]." = ? or ";
                                $z++;
                            }
                        }
                    }
                }
                $response = DB::select($query, [...$arrayValues]);
                $array = json_decode(json_encode($response), true);
            }
        }
        foreach($array as $key => $data){
            $bitMapFlag = 0; $termSerNumFlag = 0; $checkCHFlag = 0; $userFoFlag = 0;
            $userFtFlag = 0; $termTypeFlag = 0; $appVersionFlag = 0; $cvmResFlag = 0;
            $fileNamelenFlag = 0; $fileNameFlag = 0; 

            $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> ID_Code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> ID_Entry_Mode = $data['ENTRY_MODE'];
            $answer[$key] -> Bit_Map = $data['KB3_BIT_MAP'];
            $answer[$key] -> bitMapFlag = $bitMapFlag;
            $answer[$key] -> Terminal_Serial_Number = $data['KB3_TERM_SRL_NUM'];
            $answer[$key] -> termSerNumFlag = $termSerNumFlag;
            $answer[$key] -> Check_Cardholder = $data['KB3_EMV_TERM_CAP'];
            $answer[$key] -> checkCHFlag = $checkCHFlag;
            $answer[$key] -> User_Field_One = $data['KB3_USR_FLD1'];
            $answer[$key] -> userFoFlag = $userFoFlag;
            $answer[$key] -> User_Field_Two = $data['KB3_USR_FLD2'];
            $answer[$key] -> userFtFlag = $userFtFlag;
            $answer[$key] -> Terminal_Type_EMV = $data['KB3_EMV_TERM_TYPE'];
            $answer[$key] -> termTypeFlag = $termTypeFlag;
            $answer[$key] -> App_Version_Number = $data['KB3_APP_VER_NUM'];
            $answer[$key] -> appVersionFlag = $appVersionFlag;
            $answer[$key] -> CVM_Result = $data['KB3_CVM_RSLTS'];
            $answer[$key] -> cvmResFlag = $cvmResFlag;
            $answer[$key] -> File_Name_Length = $data['KB3_DF_NAME_LGTH'];
            $answer[$key] -> fileNamelenFlag = $fileNamelenFlag;
            $answer[$key] -> File_Name = $data['KB3_DF_NAME'];
            $answer[$key] -> fileNameFlag = $fileNameFlag;
            $answer[$key] -> Fiid_Card = $data['FIID_TARJ'];
            $answer[$key] -> Fiid_Comerce = $data['FIID_COMER'];
            $answer[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $answer[$key] -> Number_Sec = $data['NUM_SEC'];
            $answer[$key] -> amount = $data['MONTO1'];
        }
    }
}
