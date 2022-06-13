<?php

namespace App\Http\Controllers;

use App\Models\Kq2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class TokenB6Controller extends Controller
{
    public function index(Request $request){
        $kq2 = $request -> Kq2;
        $codeResponse = $request -> Code_Response;
        $entryMode = $request -> Entry_Mode;
        $numberFilters = 0;
        $flagKq2 = false;
        $flagCode = false;
        $flagEntry = false;
        $response = array();
        $answer = array();
        $array = array();
        $query = "select KB6_ISS_SCRIPT_DATA_LGTH, KB6_ISS_SCRIPT_DATA from test where ";

        //Detectar cuales son los filtros utilizados
        if(!empty($kq2)){ $numberFilters++; $flagKq2 = true; }
        if(!empty($codeResponse)){ $numberFilters++; $flagCode = true; }
        if(!empty($entryMode)){ $numberFilters++; $flagEntry = true; }

        switch($numberFilters){
            case 1:{ //Un solo filtro
                if($flagKq2){
                    for($i = 0; $i < count($kq2); $i++){
                        $response = array_merge($response, DB::select($query.
                        "KQ2_ID_MEDIO_ACCESO = ?", [$kq2[$i]]));
                    }
                }
                if($flagCode){
                    for($i = 0; $i < count($codeResponse); $i++){
                        $response = array_merge($response, DB::select($query.
                        "CODIGO_RESPUESTA = ?", [$codeResponse[$i]]));
                    }
                }
                if($flagEntry){
                    for($i = 0; $i < count($entryMode); $i++){
                        $response = array_merge($response, DB::select($query.
                        "ENTRY_MODE = ?", [$entryMode[$i]]));
                    }
                }
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 2:{ //Dos filtros
                if($flagKq2){
                    if($flagCode && !$flagEntry){
                        $fisrtLength = max($kq2, $codeResponse);
                        switch($fisrtLength){
                            case $kq2:{
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?",
                                        [$kq2[$i], $codeResponse[$j]]));
                                    }
                                }
                                break;
                            }
                            case $codeResponse:{
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0; $j < count($kq2); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?",
                                        [$kq2[$j], $codeResponse[$i]]));
                                    }
                                }
                                break;
                            }
                        }
                    }else{
                        $fisrtLength = max($kq2, $entryMode);
                        switch($fisrtLength){
                            case $kq2:{
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($entryMode); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?",
                                        [$kq2[$i], $entryMode[$j]]));
                                    }
                                }
                                break;
                            }
                            case $entryMode:{
                                for($i = 0; $i < count($entryMode); $i++){
                                    for($j = 0; $j < count($kq2); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?",
                                        [$kq2[$j], $entryMode[$i]]));
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
                $array = json_decode(json_encode($response), true);
                break;
            }
            case 3:{ //Tres filtros
                $fisrtLength = max($kq2, $codeResponse, $entryMode);
                switch($fisrtLength){
                    case $kq2: {
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
                            }
                        }
                        break;
                    }
                    case $codeResponse: {
                        $secondLength = max($kq2, $entryMode);
                        switch($secondLength){
                            case $kq2: {
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
                                break;
                            }
                        }
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
                        break;
                    }
                }
                $array = json_decode(json_encode($response), true);
                break;
            }
            default: {
                $response = DB::select("select KB6_ISS_SCRIPT_DATA_LGTH, KB6_ISS_SCRIPT_DATA from test");
                $array = json_decode(json_encode($response), true);
            }
        }
        
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> dataLength = $data['KB6_ISS_SCRIPT_DATA_LGTH'];
            $answer[$key] -> scriptData = $data['KB6_ISS_SCRIPT_DATA']; 
        }
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }
    public function getDataTableFilter(Request $request){
        $values = array();
        $label = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'KB6_ISS_SCRIPT_DATA_LGTH', 'KB6_ISS_SCRIPT_DATA',
        'ID_COMER', 'TERM_COMER', 'FIID_COMER', 'FIID_TERM','LN_COMER', 'LN_TERM', 'FIID_TARJ', 'LN_TARJ'];

        $values[0] = $request -> Kq2;
        $values[1] = $request -> Code_Response;
        $values[2] = $request -> Entry_Mode;
        $values[3] = $request -> dataL;
        $values[4] = $request -> SctData;
        $values[5] = $request -> ID_Comer;
        $values[6] = $request -> Term_Comer; 
        $values[7] = $request -> Fiid_Comer;
        $values[8] = $request -> Fiid_Term;
        $values[9] = $request -> Ln_Comer;
        $values[10] = $request -> Ln_Term;
        $values[11] = $request -> Fiid_Card; 
        $values[12] = $request -> Ln_Card;
        $answer = array();
        $response = array();
        $arrayValues = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB6_ISS_SCRIPT_DATA_LGTH, KB6_ISS_SCRIPT_DATA, ID_COMER, TERM_COMER, 
        FIID_COMER, FIID_TERM, LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 from test where ";
        $queryOutFilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB6_ISS_SCRIPT_DATA_LGTH, KB6_ISS_SCRIPT_DATA, ID_COMER, TERM_COMER, 
        FIID_COMER, FIID_TERM, LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 from test";
        
        //Detectar cuales son los filtros utilizados
        for($key = 0; $key < 13; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($label[$key]);
            }
        }

        $filteredValues = array_values($values);
        $filteredLabels = array_values($label);

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
                //Ingresar todos los valores del filtro en un solo arreglo ($filteredValues)
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        array_push($arrayValues, $filteredValues[$i][$j]);
                    }
                }
                $z = 1; //Variable controladora de la longitud y contrucción del query
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
            $answer[$key] -> dataLength = $data['KB6_ISS_SCRIPT_DATA_LGTH'];
            $answer[$key] -> scriptData = $data['KB6_ISS_SCRIPT_DATA'];
            $answer[$key] -> ID_Comer = $data['ID_COMER'];
            $answer[$key] -> Term_Comer = $data['TERM_COMER'];
            $answer[$key] -> Fiid_Comer = $data['FIID_COMER'];
            $answer[$key] -> Fiid_Term = $data['FIID_TERM'];
            $answer[$key] -> Ln_Comer = $data['LN_COMER'];
            $answer[$key] -> Ln_Term = $data['LN_TERM'];
            $answer[$key] -> Fiid_Card = $data['FIID_TARJ'];
            $answer[$key] -> Ln_Card = $data['LN_TARJ'];
            $answer[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $answer[$key] -> Number_Sec = $data['NUM_SEC'];
            //Separación de la cifra decimal y entera para el monto
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $answer[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
        }
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }
}
