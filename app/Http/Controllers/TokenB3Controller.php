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
        KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME, ID_COMER, TERM_COMER, FIID_COMER,
        FIID_TERM, LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ from test where ";

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
                KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME,  
                ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ from test");
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
            $answer[$key] -> ID_Comer = $data['ID_COMER'];
            $answer[$key] -> Term_Comer = $data['TERM_COMER'];
            $answer[$key] -> Fiid_Comer = $data['FIID_COMER'];
            $answer[$key] -> Fiid_Term = $data['FIID_TERM'];
            $answer[$key] -> Ln_Comer = $data['LN_COMER'];
            $answer[$key] -> Ln_Term = $data['LN_TERM'];
            $answer[$key] -> Fiid_Card = $data['FIID_TARJ'];
            $answer[$key] -> Ln_Card = $data['LN_TARJ'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }

    public function getDataTableFilter(Request $request){
        $values = array();
        $label = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'KB3_BIT_MAP', 'KB3_TERM_SRL_NUM', 'KB3_EMV_TERM_CAP', 'KB3_USR_FLD1', 'KB3_USR_FLD2',
        'KB3_EMV_TERM_TYPE', 'KB3_APP_VER_NUM', 'KB3_CVM_RSLTS', 'KB3_DF_NAME_LGTH', 'KB3_DF_NAME', 'ID_COMER', 'TERM_COMER', 'FIID_COMER', 'FIID_TERM',
        'LN_COMER', 'LN_TERM', 'FIID_TARJ', 'LN_TARJ'];
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
        $values[13] = $request -> ID_Comer;
        $values[14] = $request -> Term_Comer;
        $values[15] = $request -> Fiid_Comer;
        $values[16] = $request -> Ln_Comer;
        $values[17] = $request -> Ln_Term;
        $values[18] = $request -> Fiid_Card;
        $values[19] = $request -> Ln_Card; 

        $response = array();
        $answer = array();
        $answerAllRigth = array();
        $arrayValues = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB3_BIT_MAP, KB3_TERM_SRL_NUM, KB3_EMV_TERM_CAP, KB3_USR_FLD1, KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, 
        KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 from test where ";

        $queryOutFilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB3_BIT_MAP, KB3_TERM_SRL_NUM, KB3_EMV_TERM_CAP, KB3_USR_FLD1, KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, 
        KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 from test";

        //Detectar cuales son los filtros utilizados
        for($key = 0; $key < 20; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($label[$key]);
            }
        }

        $filteredValues = array_values($values);
        $filteredLabels = array_values($label);

        for($i = 0; $i < count($filteredValues); $i++){
            for($j = 0; $j < count($filteredValues[$i]); $j++){
                if($filteredValues[$i][$j] === null){
                    $filteredValues[$i][$j] = " ";
                }
            }
        }

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

            //Validación de todos los campos
            if(($data['ENTRY_MODE'] > 49 && $data['ENTRY_MODE'] < 53) || $data['ENTRY_MODE'] > 69 && $data['ENTRY_MODE'] < 72){
                if(strlen($data['KB3_BIT_MAP']) == 4){
                    $bitMapFlag = 1;
                    if(strlen($data['KB3_TERM_SRL_NUM']) == 8){ $termSerNumFlag = 1; }
                    if(strlen($data['KB3_EMV_TERM_CAP']) == 8){ $checkCHFlag = 1; }
                    if(strlen($data['KB3_USR_FLD1']) == 4){ $userFoFlag = 1; }
                    if(strlen($data['KB3_USR_FLD2']) == 8){ $userFtFlag = 1; }
                    if(strlen($data['KB3_EMV_TERM_TYPE']) == 2){ $termTypeFlag = 1; }
                    if(strlen($data['KB3_APP_VER_NUM']) == 4){ $appVersionFlag = 1; }
                    if(strlen($data['KB3_CVM_RSLTS']) == 6 ){ $cvmResFlag = 1; }
                    if(strlen($data['KB3_DF_NAME_LGTH']) == 4){ $fileNamelenFlag = 1; }
                    if(strlen($data['KB3_DF_NAME']) == 32){ $fileNameFlag = 1; }
                }
            }else{
                if(strlen($data['KB3_BIT_MAP']) == 1 || $data['KB3_BIT_MAP'] == ""){ $bitMapFlag = 1; }
                if(strlen($data['KB3_TERM_SRL_NUM']) == 1 || $data['KB3_TERM_SRL_NUM'] == ""){ $termSerNumFlag = 1; }
                if(strlen($data['KB3_EMV_TERM_CAP']) == 1 || $data['KB3_EMV_TERM_CAP'] == ""){ $checkCHFlag = 1; }
                if(strlen($data['KB3_USR_FLD1']) == 1 || $data['KB3_USR_FLD1'] == ""){ $userFoFlag = 1; }
                if(strlen($data['KB3_USR_FLD2']) == 1 || $data['KB3_USR_FLD2'] == ""){ $userFtFlag = 1; }
                if(strlen($data['KB3_EMV_TERM_TYPE']) == 1 || $data['KB3_EMV_TERM_TYPE'] == ""){ $termTypeFlag = 1; }
                if(strlen($data['KB3_APP_VER_NUM']) == 1 || $data['KB3_APP_VER_NUM'] == ""){ $appVersionFlag = 1; }
                if(strlen($data['KB3_CVM_RSLTS']) == 1 || $data['KB3_CVM_RSLTS'] == ""){ $cvmResFlag = 1; }
                if(strlen($data['KB3_DF_NAME_LGTH']) == 1 || $data['KB3_DF_NAME_LGTH'] == ""){ $fileNamelenFlag = 1; }
                if(strlen($data['KB3_DF_NAME']) == 1 || $data['KB3_DF_NAME'] == ""){ $fileNameFlag = 1; }
            }

            if($bitMapFlag == 0 || $termSerNumFlag == 0 || $checkCHFlag == 0 || $userFoFlag == 0||
                $userFtFlag == 0 || $termTypeFlag == 0 || $appVersionFlag == 0 || $cvmResFlag == 0 ||
                $fileNamelenFlag == 0 || $fileNameFlag == 0){

                $answer[$key] = new stdClass();
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
                //Separación del decimal y del entero en el monto
                $dec = substr($data['MONTO1'], strlen($data['MONTO1'])-2, 2);
                $int = substr($data['MONTO1'], 0, $data['MONTO1']-2);
                $answer[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
                $answer[$key]->ID_Comer = $data['ID_COMER'];
                $answer[$key]->Term_Comer = $data['TERM_COMER'];
                $answer[$key]->Fiid_Comer = $data['FIID_COMER'];
                $answer[$key]->Fiid_Term = $data['FIID_TERM'];
                $answer[$key]->Ln_Comer = $data['LN_COMER'];
                $answer[$key]->Ln_Term = $data['LN_TERM'];
                $answer[$key]->Fiid_Card = $data['FIID_TARJ'];
                $answer[$key]->Ln_Card = $data['LN_TARJ'];
            }else {
                $answerAllRigth[$key] = new stdClass();
                $answerAllRigth[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                $answerAllRigth[$key] -> ID_Code_Response = $data['CODIGO_RESPUESTA'];
                $answerAllRigth[$key] -> ID_Entry_Mode = $data['ENTRY_MODE'];
                $answerAllRigth[$key] -> Bit_Map = $data['KB3_BIT_MAP'];
                $answerAllRigth[$key] -> bitMapFlag = $bitMapFlag;
                $answerAllRigth[$key] -> Terminal_Serial_Number = $data['KB3_TERM_SRL_NUM'];
                $answerAllRigth[$key] -> termSerNumFlag = $termSerNumFlag;
                $answerAllRigth[$key] -> Check_Cardholder = $data['KB3_EMV_TERM_CAP'];
                $answerAllRigth[$key] -> checkCHFlag = $checkCHFlag;
                $answerAllRigth[$key] -> User_Field_One = $data['KB3_USR_FLD1'];
                $answerAllRigth[$key] -> userFoFlag = $userFoFlag;
                $answerAllRigth[$key] -> User_Field_Two = $data['KB3_USR_FLD2'];
                $answerAllRigth[$key] -> userFtFlag = $userFtFlag;
                $answerAllRigth[$key] -> Terminal_Type_EMV = $data['KB3_EMV_TERM_TYPE'];
                $answerAllRigth[$key] -> termTypeFlag = $termTypeFlag;
                $answerAllRigth[$key] -> App_Version_Number = $data['KB3_APP_VER_NUM'];
                $answerAllRigth[$key] -> appVersionFlag = $appVersionFlag;
                $answerAllRigth[$key] -> CVM_Result = $data['KB3_CVM_RSLTS'];
                $answerAllRigth[$key] -> cvmResFlag = $cvmResFlag;
                $answerAllRigth[$key] -> File_Name_Length = $data['KB3_DF_NAME_LGTH'];
                $answerAllRigth[$key] -> fileNamelenFlag = $fileNamelenFlag;
                $answerAllRigth[$key] -> File_Name = $data['KB3_DF_NAME'];
                $answerAllRigth[$key] -> fileNameFlag = $fileNameFlag;
                $answerAllRigth[$key] -> Fiid_Card = $data['FIID_TARJ'];
                $answerAllRigth[$key] -> Fiid_Comerce = $data['FIID_COMER'];
                $answerAllRigth[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
                $answerAllRigth[$key] -> Number_Sec = $data['NUM_SEC'];
                //Separación del decimal y el entero en el monto
                $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
                $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
                $answerAllRigth[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
                $answerAllRigth[$key]->ID_Comer = $data['ID_COMER'];
                $answerAllRigth[$key]->Term_Comer = $data['TERM_COMER'];
                $answerAllRigth[$key]->Fiid_Comer = $data['FIID_COMER'];
                $answerAllRigth[$key]->Fiid_Term = $data['FIID_TERM'];
                $answerAllRigth[$key]->Ln_Comer = $data['LN_COMER'];
                $answerAllRigth[$key]->Ln_Term = $data['LN_TERM'];
                $answerAllRigth[$key]->Fiid_Card = $data['FIID_TARJ'];
                $answerAllRigth[$key]->Ln_Card = $data['LN_TARJ'];
            }
        }
        $badAnswer = array_values($answer);
        $goodAnswer = array_values($answerAllRigth);
        $generalResponse = array_merge($badAnswer, $goodAnswer);
        $arrayJson = json_decode(json_encode($generalResponse), true);
        return $arrayJson;
    }
}
