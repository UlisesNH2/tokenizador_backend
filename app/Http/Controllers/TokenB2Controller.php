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
        $query = "select KB2_BIT_MAP,KB2_USR_FLD1,KB2_ARQC,KB2_AMT_AUTH,KB2_AMT_OTHER, KB2_ATC,KB2_TERM_CTRY_CDE,
        KB2_TRAN_CRNCY_CDE,KB2_TRAN_DAT,KB2_TRAN_TYPE,KB2_UNPREDICT_NUM,KB2_ISS_APPL_DATA_LGTH,KB2_ISS_APPL_DATA,
        KB2_CRYPTO_INFO_DATA,KB2_TVR,KB2_AIP from test where ";

        //Detectar los filtros utilizados
        if(!empty($kq2)){ $numberFilters++; $flagkq2 = true; }
        if(!empty($codeResponse)){ $numberFilters++; $flagCode = true; }
        if(!empty($entryMode)){ $numberFilters++; $flagEntry = true; }

        switch($numberFilters){
            case 1:{ //Un solo filtro detectado
                if($flagkq2){
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
            case 2:{ //Dos filtros detectado
                if($flagkq2){
                    if($flagCode && !$flagEntry){
                        $firstLenght = max($kq2, $codeResponse);
                        switch($firstLenght){
                            case $kq2:{
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?", [$kq2[$i], $codeResponse[$j]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                            case $codeResponse:{
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0; $j < count($kq2); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?", [$kq2[$j], $codeResponse[$i]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                        }
                    }else{
                        if(!$flagCode && $flagEntry){
                            $firstLenght = max($kq2, $entryMode);
                            switch($firstLenght){
                                case $kq2:{
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?", [$kq2[$i], $entryMode[$j]]));
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $entryMode:{
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            $response = array_merge($response, DB::select($query.
                                            "KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?", [$kq2[$j], $entryMode[$i]]));
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
                        $firstLenght = max($codeResponse, $entryMode);
                        switch($firstLenght){
                            case $codeResponse:{
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0; $j < count($entryMode); $j++){
                                        $response = array_merge($response, DB::select($query.
                                        "CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", [$codeResponse[$i], $entryMode[$j]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                            case $entryMode:{
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
            case 3:{ //Tres filtros detectados
                if($flagkq2 && $flagCode && $flagEntry){
                    $firstLenght = max($kq2, $codeResponse, $entryMode);
                    switch($firstLenght){
                        case $kq2:{
                            $secondLenght = max($codeResponse, $entryMode);
                            switch($secondLenght){
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
                                    $array = json_decode(json_encode($response), true);
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
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                            }
                            break;
                        }
                        case $codeResponse:{
                            $secondLenght = max($kq2, $entryMode);
                            switch($secondLenght){
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
                                case $entryMode:{
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
                            $secondLenght = max($kq2, $codeResponse);
                            switch($secondLenght){
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
                                    $array =json_decode(json_encode($response), true);
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
                $response = DB::select("select KB2_BIT_MAP,KB2_USR_FLD1,KB2_ARQC,KB2_AMT_AUTH,KB2_AMT_OTHER, KB2_ATC,KB2_TERM_CTRY_CDE,
                KB2_TRAN_CRNCY_CDE,KB2_TRAN_DAT,KB2_TRAN_TYPE,KB2_UNPREDICT_NUM,KB2_ISS_APPL_DATA_LGTH,KB2_ISS_APPL_DATA,
                KB2_CRYPTO_INFO_DATA,KB2_TVR,KB2_AIP from test");
                $array = json_decode(json_encode($response), true);
            }
        }

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> Bit_Map = $data['KB2_BIT_MAP'];
            $answer[$key] -> User_Field_One = $data['KB2_USR_FLD1'];
            $answer[$key] -> Crypto_Data = $data['KB2_CRYPTO_INFO_DATA'];
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
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }

    public function getDataTableFilter(Request $request){
        $values = array();
        $label = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE','KB2_BIT_MAP', 'KB2_USR_FLD1', 'KB2_ARQC', 'KB2_AMT_AUTH', 'KB2_AMT_OTHER', 'KB2_ATC',
        'KB2_TERM_CTRY_CDE', 'KB2_TRAN_CRNCY_CDE', 'KB2_TRAN_DAT', 'KB2_TRAN_TYPE', 'KB2_UNPREDICT_NUM',
        'KB2_ISS_APPL_DATA_LGTH', 'KB2_ISS_APPL_DATA', 'KB2_TVR', 'KB2_AIP', 'ID_COMER', 'TERM_COMER', 'FIID_COMER', 'FIID_TERM',
        'LN_COMER', 'LN_TERM', 'FIID_TARJ', 'LN_TARJ'];

        $values[0] = $request -> Kq2;
        $values[1] = $request -> Code_Response;
        $values[2] = $request -> Entry_Mode;
        $values[3] = $request -> Bit_Map;
        $values[4] = $request -> User_Field_One;
        $values[5] = $request -> ARQC;
        $values[6] = $request -> AMT_Auth;
        $values[7] = $request -> AMT_Other;
        $values[8] = $request -> ATC;
        $values[9] = $request -> Terminal_Country_Code;
        $values[10] = $request -> Terminal_Currency_Code;
        $values[11] = $request -> Transaction_Date;
        $values[12] = $request -> Transaction_Type;
        $values[13] = $request -> Umpedict_Number;
        $values[14] = $request -> Issuing_App_Data_Length;
        $values[15] = $request -> Issuing_App_Data;
        $values[16] = $request -> TVR;
        $values[17] = $request -> AIP;
        $values[18] = $request -> ID_Comer;
        $values[19] = $request -> Term_Comer;
        $values[20] = $request -> Fiid_Comer;
        $values[21] = $request -> Fiid_Term;
        $values[22] = $request -> Ln_Comer;
        $values[23] = $request -> Fiid_Card;
        $values[24] = $request -> Ln_Card;

        $answer = array();
        $array = array();
        $response = array();
        $arrayValues = array();
        $answer = array();
        $answerOk = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB2_BIT_MAP,KB2_USR_FLD1,KB2_ARQC,KB2_AMT_AUTH,KB2_AMT_OTHER, KB2_ATC,KB2_TERM_CTRY_CDE,
        KB2_TRAN_CRNCY_CDE,KB2_TRAN_DAT,KB2_TRAN_TYPE,KB2_UNPREDICT_NUM,KB2_ISS_APPL_DATA_LGTH,KB2_ISS_APPL_DATA,
        KB2_CRYPTO_INFO_DATA,KB2_TVR,KB2_AIP, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 from test where ";

        $queryOutfilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB2_BIT_MAP,KB2_USR_FLD1,KB2_ARQC,KB2_AMT_AUTH,KB2_AMT_OTHER, KB2_ATC,KB2_TERM_CTRY_CDE,
        KB2_TRAN_CRNCY_CDE,KB2_TRAN_DAT,KB2_TRAN_TYPE,KB2_UNPREDICT_NUM,KB2_ISS_APPL_DATA_LGTH,KB2_ISS_APPL_DATA,
        KB2_CRYPTO_INFO_DATA,KB2_TVR,KB2_AIP, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 from test";

        //Detectar cuales son los filtros utilizados
        for($key = 0; $key < 25; $key++){
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
            $response = DB::select($queryOutfilters);
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
            $answer[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> entryMode = $data['ENTRY_MODE'];
            $answer[$key] -> bitMapB2 = $data['KB2_BIT_MAP'];
            $answer[$key] -> UsrFO = $data['KB2_USR_FLD1'];
            $answer[$key] -> CrypData = $data['KB2_CRYPTO_INFO_DATA'];
            $answer[$key] -> ARQC = $data['KB2_ARQC'];
            $answer[$key] -> AMTAuth = $data['KB2_AMT_AUTH'];
            $answer[$key] -> AMTOther = $data['KB2_AMT_OTHER'];
            $answer[$key] -> ATC = $data['KB2_ATC'];
            $answer[$key] -> TermCounCode = $data['KB2_TERM_CTRY_CDE'];
            $answer[$key] -> TermCurrCode = $data['KB2_TRAN_CRNCY_CDE'];
            $answer[$key] -> TranDate = $data['KB2_TRAN_DAT'];
            $answer[$key] -> TranType = $data['KB2_TRAN_TYPE'];
            $answer[$key] -> UmpNum = $data['KB2_UNPREDICT_NUM'];
            $answer[$key] -> IssAppDataLen = $data['KB2_ISS_APPL_DATA_LGTH'];
            $answer[$key] -> IssAppData = $data['KB2_ISS_APPL_DATA'];
            $answer[$key] -> TVR = $data['KB2_TVR'];
            $answer[$key] -> AIP = $data['KB2_AIP'];
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
