<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;
use Illuminate\Support\Facades\DB;

class TokenB5Controller extends Controller
{
    public function index(Request $request){
        $kq2 = $request -> Kq2;
        $codeResponse = $request -> Code_Response;
        $entryMode = $request -> Entry_Mode;
        $numberFilters = 0;
        $flagkq2 = false;
        $flagCode = false;
        $flagEntry = false;
        $response = array();
        $answer = array();
        $array = array(); //quitar despues
        $query = "select KB5_ISS_AUTH_DATA_LGTH, KB5_ARPC, KB5_CRD_STAT_UPDT, KB5_ADDL_DATA, KB5_SEND_CRD_BLK, KB5_SEND_PUT_DATA
        from test where ";

        //Detectar los filtros que son utilizados
        if(!empty($kq2)){ $numberFilters++; $flagkq2 = true;}
        if(!empty($codeResponse)){ $numberFilters++; $flagCode = true; }
        if(!empty($entryMode)){ $numberFilters++; $flagEntry = true; }

        switch($numberFilters){
            case 1: {
                if($flagkq2){
                    for($i = 0; $i < count($kq2); $i++){
                        $response = array_merge($response, DB::select($query.
                        "KQ2_ID_MEDIO_ACCESO = ?", [$kq2[$i]]));
                    }
                    if($flagCode){
                        for($i = 0; $i < count($codeResponse); $i++){
                            $response = array_merge($response, DB::select($query.
                            "CODIGO_RESPUESTA = ?", [$codeResponse[$i]]));
                        }
                    }
                    if($flagEntry){
                        for($i = 0; $i < count($entryMode); $i++){
                            $response = array_merge($response, DB::select($query,
                            "ENTRY_MODE = ?", [$entryMode[$i]]));
                        }
                    }
                    $array = json_decode(json_encode($response), true);
                }
                break;
            }
            case 2: {
                if($flagkq2){
                    if($flagCode && !$flagEntry){
                        $firstLength = max($kq2, $codeResponse);
                        switch($firstLength){
                            case $kq2:{
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?", [$kq2[$i], $codeResponse[$j]]));
                                    }
                                }
                                break;
                            }
                            case $codeResponse:{
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0; $j < count($kq2); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?", [$kq2[$j], $codeResponse[$i]]));
                                    }
                                }
                                break;
                            }
                        }
                        $array = json_decode(json_encode($response), true);
                    }else{
                        $firstLength = max($kq2, $entryMode);
                        switch($firstLength){
                            case $kq2: {
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($entryMode); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?", [$kq2[$j], $entryMode[$j]]));
                                    }
                                }
                                break;
                            }
                            case $entryMode: {
                                for($i = 0; $i < count($entryMode); $i++){
                                    for($j = 0; $j < count($kq2); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?", [$kq2[$j], $entryMode[$i]]));
                                    }
                                }
                                break;
                            }
                        }
                    }
                    $array = json_decode(json_encode($response), true);
                }
                break;
            }
            case 3:{
                $firstLength = max($kq2, $codeResponse, $entryMode);
                switch($firstLength){
                    case $kq2:{
                        $secondLength = max($codeResponse, $entryMode);
                        switch($secondLength){
                            case $codeResponse:{
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        for($z = 0; $z < count($entryMode); $z++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                            [$kq2[$i], $codeResponse[$j], $entryMode[$z]]));
                                        }
                                    }
                                }
                                break;
                            }
                            case $entryMode:{
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($entryMode); $j++){
                                        for($z = 0; $z < count($codeResponse); $z++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                            [$kq2[$i], $codeResponse[$z], $entryMode[$j]]));
                                        }
                                    }
                                }
                                break;
                            }
                            $array = json_decode(json_encode($response), true);
                        }
                        break;
                    }
                    case $codeResponse:{
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
                                break;
                            }
                            case $entryMode:{
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0;  $j < count($entryMode); $j++){
                                        for($z = 0; $z < count($kq2); $z++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", 
                                            [$kq2[$z], $codeResponse[$i], $entryMode[$j]]));
                                        }
                                    }
                                }
                                break;
                            }
                        }
                        $array = json_decode(json_encode($response), true);
                        break;
                    }
                    case $entryMode: {
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
                                break;
                            }
                            case $codeResponse:{
                                for($i = 0; $i < count($entryMode); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        for($z = 0; $z < count($kq2); $z++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                            [$kq2[$z], $codeResponse[$j], $entryMode[$i]]));
                                        }
                                    }
                                }
                                break;
                            }
                        }
                        $array = json_decode(json_encode($response), true);
                        break;
                    }
                }
            }
            default:{
                $response = DB::select("select KB5_ISS_AUTH_DATA_LGTH, KB5_ARPC, KB5_CRD_STAT_UPDT, KB5_ADDL_DATA, KB5_SEND_CRD_BLK, KB5_SEND_PUT_DATA
                from test");
                $array = json_decode(json_encode($response), true);
                break;
            }
        }
        
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> Iss_Auth_Data = $data['KB5_ISS_AUTH_DATA_LGTH'];
            $answer[$key] -> arpc = $data['KB5_ARPC'];
            $answer[$key] -> Card_Update = $data['KB5_CRD_STAT_UPDT'];
            $answer[$key] -> Addl_Data = $data['KB5_ADDL_DATA'];
            $answer[$key] -> Send_Card = $data['KB5_SEND_CRD_BLK'];
            $answer[$key] -> Send_Data = $data['KB5_SEND_PUT_DATA'];
        }
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }

    public function getDataTableFilter(Request $request){
        $values = array();
        $label = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'KB5_ISS_AUTH_DATA_LGTH', 'KB5_ARPC', 'KB5_CRD_STAT_UPDT', 'KB5_ADDL_DATA', 'KB5_SEND_CRD_BLK', 'KB5_SEND_PUT_DATA', 
        'ID_COMER', 'TERM_COMER', 'FIID_COMER', 'FIID_TERM','LN_COMER', 'LN_TERM', 'FIID_TARJ', 'LN_TARJ'];

        $values[0] = $request -> Kq2;
        $values[1] = $request -> Code_Response;
        $values[2] = $request -> Entry_Mode;
        $values[3] = $request -> Iss_Auth_Data;
        $values[4] = $request -> arpc;
        $values[5] = $request -> Card_update;
        $values[6] = $request -> Addl_Data;
        $values[7] = $request -> Send_Card;
        $values[8] = $request -> Send_Data;
        $values[9] = $request -> ID_Comer;
        $values[10] = $request -> Term_Comer;
        $values[11] = $request -> Fiid_Comer;
        $values[12] = $request -> Fiid_Term;
        $values[13] = $request -> Ln_Comer;
        $values[14] = $request -> Ln_Term;
        $values[15] = $request -> Fiid_Card;
        $values[16] = $request -> Ln_Card;

        $answer = array();
        $answerAllRigth = array();
        $array = array();
        $response = array();
        $arrayValues = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB5_ISS_AUTH_DATA_LGTH, KB5_ARPC, KB5_CRD_STAT_UPDT, KB5_ADDL_DATA, KB5_SEND_CRD_BLK, KB5_SEND_PUT_DATA, 
        ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 
        from test where ";
        $queryOutFilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB5_ISS_AUTH_DATA_LGTH, KB5_ARPC, KB5_CRD_STAT_UPDT, KB5_ADDL_DATA, KB5_SEND_CRD_BLK, KB5_SEND_PUT_DATA, 
        ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 
        from test";

        //Detectar cuales son los filtros utilizados
        for($key = 0; $key < 17; $key++){
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
        if(empty($filteredValues)){
            $response = DB::select($queryOutFilters);
            $array = json_decode(json_encode($response), true);
        }else{
            if(count($filteredValues) <= 1){
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        $response = array_merge($response, DB::select($query.$filteredLabels[$i]." = ?",
                        [$filteredValues[$i][$j]]));
                    }
                }
                $array = json_decode(json_encode($response), true);
            }else{
                //Ingresar todos los valores de $filteredValues en un solo arreglo
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        array_push($arrayValues, $filteredValues[$i][$j]);
                    }
                }
                $z = 1; //Variable para el control de la longitud del query
                //Construcción del query de acuerdo a los filtros seleccionados
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
            $answer[$key] = new stdClass();
            $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> ID_Code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> ID_Entry_Mode = $data['ENTRY_MODE'];
            $answer[$key] -> Iss_Auth_Data = $data['KB5_ISS_AUTH_DATA_LGTH'];
            $answer[$key] -> arpc = $data['KB5_ARPC'];
            $answer[$key] -> Card_update = $data['KB5_CRD_STAT_UPDT'];
            $answer[$key] -> Addl_Data = $data['KB5_ADDL_DATA'];
            $answer[$key] -> Send_Card = $data['KB5_SEND_CRD_BLK'];
            $answer[$key] -> Send_Data = $data['KB5_SEND_PUT_DATA'];
            $answer[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $answer[$key] -> Number_Sec = $data['NUM_SEC'];
            //Separación de la cifra decimal y entero del monto
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $answer[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
            $answer[$key] -> ID_Comer = $data['ID_COMER'];
            $answer[$key] -> Term_Comer = $data['TERM_COMER'];
            $answer[$key] -> Fiid_Comer = $data['FIID_COMER'];
            $answer[$key] -> Fiid_Term = $data['FIID_TERM'];
            $answer[$key] -> Ln_Comer = $data['LN_COMER'];
            $answer[$key] -> Ln_Term = $data['LN_TERM'];
            $answer[$key] -> Fiid_Card = $data['FIID_TARJ'];
            $answer[$key] -> Ln_Card = $data['LN_TARJ'];
        }
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }
}
