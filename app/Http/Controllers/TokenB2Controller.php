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
            $bitMapFlag = 0; $userFOFlag = 0; $arqcFlag = 0; $amtAuthFlag = 0;
            $amtOtherFlag = 0; $atcFlag = 0; $termConFlag = 0; $termCurrFlag = 0;
            $transDateFlag = 0; $transTypeFlag = 0; $umpNumFlag = 0; $tvrFlag = 0;
            $appDataLenFlag = 0; $appDataFlag = 0; $cryptoFlag = 0; $aipFlag = 0;
            //Conversión a binario del campo Crypto_Data, se rellenan los espacios con ceros binarios
            $cryptoDataBinary = str_pad(base_convert($data['KB2_CRYPTO_INFO_DATA'],16,2),8,'0',STR_PAD_LEFT);
            //Conversión del dato binario a un arreglo para su posterior validación
            $arrayCrypto = str_split($cryptoDataBinary, 1);

            $tvrDataBinary = str_pad(base_convert($data['KB2_TVR'],16,2),40,'0',STR_PAD_LEFT);
            $arrayTvr = str_split($tvrDataBinary, 1);

            $aipDataBinary = str_pad(base_convert($data['KB2_AIP'],16,2),16,'0',STR_PAD_LEFT);
            $arrayAip = str_split($aipDataBinary, 1);
            $fisrtCombinationNumber = base_convert('4', 10, 2);

            if(($data['ENTRY_MODE'] > 49 && $data['ENTRY_MODE'] < 53) || ($data['ENTRY_MODE'] > 69 && $data['ENTRY_MODE'] < 72)){
                if(strlen($data['KB2_BIT_MAP']) == 4){
                    $bitMapFlag = 1;
                    if(strlen($data['KB2_USR_FLD1']) == 4){ $userFOFlag = 1; };
                    if(strlen($data['KB2_ARQC']) == 16) { $arqcFlag = 1; }
                    if(strlen($data['KB2_AMT_AUTH']) == 12) { $amtAuthFlag = 1; }
                    if(strlen($data['KB2_AMT_OTHER']) == 12) { $amtOtherFlag = 1; }
                    if(strlen($data['KB2_ATC']) == 4) { $atcFlag = 1; }
                    if(strlen($data['KB2_TERM_CTRY_CDE']) == 3) { $termConFlag = 1; }
                    if(strlen($data['KB2_TRAN_CRNCY_CDE']) == 3) { $termCurrFlag = 1; }
                    if(strlen($data['KB2_TRAN_DAT']) == 6) { $transDateFlag = 1; }
                    if(strlen($data['KB2_TRAN_TYPE']) == 2) { $transTypeFlag = 1; }
                    if(strlen($data['KB2_UNPREDICT_NUM']) == 8) { $umpNumFlag = 1; }
                    if(strlen($data['KB2_ISS_APPL_DATA_LGTH']) == 4) { $appDataLenFlag = 1; }
                    if(strlen($data['KB2_ISS_APPL_DATA']) == 64) { $appDataFlag = 1; }
                    //Comparación con cada uno de los dígitos del campo.
                    if(strlen($data['KB2_CRYPTO_INFO_DATA']) == 2){
                        if($arrayCrypto[0].$arrayCrypto[1] < $fisrtCombinationNumber){
                            if($arrayCrypto[4] == 0 || $arrayCrypto[4] == 1){
                                if($arrayCrypto[5] !== 0){
                                    if(($arrayCrypto[6].$arrayCrypto[7] < $fisrtCombinationNumber)){
                                        $cryptoFlag = 1;
                                    }
                                }
                            } 
                        }
                    }
                    //Validación del campo TVR
                    if(strlen($data['KB2_TVR']) == 10){
                        $firstByteFlag = false;
                        $secondByteFlag = false;
                        $thirdBtyeFlag = false;
                        $fourthByteFlag = false;
                        $fifthByteFlag = false;
                        //Validación del primer byte (8 primeras posiciones) del campo TVR
                        //Solo se valida de la posición 0 a 4 (0-7)
                        for($i = 0; $i < 5; $i++){
                            if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                                $firstByteFlag = true;
                            }else{ $firstByteFlag = false; $i = 6;}
                        }
                        //Validación del segundo byte (8 segundas posiciones)
                        //Sólo se valida de la posición 0 a 4 (8-15)
                        if($firstByteFlag){
                            for($i = 8; $i < 13; $i++){
                                if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                                    $secondByteFlag = true;
                                }else { $secondByteFlag = false; $i = 14;}
                            }
                        }
                        //Validación del tercer byte (8 terceras posiciones)
                        //Sólo se valida de la posición 0 a 5 (16-23)
                        if($secondByteFlag){
                            for($i = 16; $i < 22; $i++){
                                if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                                    $thirdBtyeFlag = true;
                                }else { $thirdBtyeFlag = false; $i = 23;}
                            }
                        }
                        //Validación del cuarto byte (8 cuartas posiciones)
                        //Solo se valida de la posición 0 a 4 (24-31)
                        if($thirdBtyeFlag){
                            for($i = 24; $i < 29; $i++){
                                if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                                    $fourthByteFlag = true;
                                }else { $fourthByteFlag = false; $i = 30;}
                            }
                        }
                        //Validación del quinto byte (8 quintas posiciones)
                        //Solo se valida de la posición 0 a 3 (32-39)
                        if($fourthByteFlag){
                            for($i = 32; $i < 35; $i++){
                                if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                                    $fifthByteFlag = true;
                                }else { $fifthByteFlag = false; $i = 36;}
                            }
                        }
                        if($fifthByteFlag){ $tvrFlag = 1;} 
                    }
    
                    //Validación del campo AIP
                    if(strlen($data['KB2_AIP']) == 4){
                        //Validación del primer byte (8 primeras posiciones)
                        //Solo se valida de la posición 0 a 5
                        $byteFlag = false;
                        for($i = 0; $i < 6; $i++){
                            if($arrayAip[$i] == 0 || $arrayAip[$i] == 1){
                                $byteFlag = true;
                            }else { $byteFlag = false;  $i = 7;}
                        }
                        if($byteFlag){ $aipFlag = 1; }
                    }
            }
        }else{
            if(strlen($data['KB2_BIT_MAP']) == 1 || $data['KB2_BIT_MAP'] == ""){ $bitMapFlag = 1; }
            if(strlen($data['KB2_USR_FLD1']) == 1 || $data['KB2_USR_FLD1'] == ""){ $userFOFlag = 1; }
            if(strlen($data['KB2_ARQC']) == 1 || $data['KB2_ARQC'] == ""){ $arqcFlag = 1; }
            if(strlen($data['KB2_AMT_AUTH']) == 1 || $data['KB2_AMT_AUTH'] == ""){ $amtAuthFlag = 1; }
            if(strlen($data['KB2_AMT_OTHER']) == 1 || $data['KB2_AMT_OTHER'] == ""){ $amtOtherFlag = 1; }
            if(strlen($data['KB2_ATC']) == 1 || $data['KB2_ATC'] == ""){ $atcFlag = 1; }
            if(strlen($data['KB2_TERM_CTRY_CDE']) == 1 || $data['KB2_TERM_CTRY_CDE'] == ""){ $termConFlag = 1; }
            if(strlen($data['KB2_TRAN_CRNCY_CDE']) == 1 || $data['KB2_TRAN_CRNCY_CDE'] == ""){ $termCurrFlag = 1; }
            if(strlen($data['KB2_TRAN_DAT']) == 1 || $data['KB2_TRAN_DAT'] == ""){ $transDateFlag = 1; }
            if(strlen($data['KB2_TRAN_TYPE']) == 1 || $data['KB2_TRAN_TYPE'] == ""){ $transTypeFlag = 1; }
            if(strlen($data['KB2_UNPREDICT_NUM']) == 1 || $data['KB2_UNPREDICT_NUM'] == ""){ $umpNumFlag = 1; }
            if(strlen($data['KB2_ISS_APPL_DATA_LGTH']) == 1 || $data['KB2_ISS_APPL_DATA_LGTH'] == ""){ $appDataLenFlag = 1; }
            if(strlen($data['KB2_ISS_APPL_DATA']) == 1 || $data['KB2_ISS_APPL_DATA'] == ""){ $appDataFlag = 1; }
            if(strlen($data['KB2_CRYPTO_INFO_DATA']) == 1 || $data['KB2_CRYPTO_INFO_DATA'] == ""){ $cryptoFlag = 1; }
            if(strlen($data['KB2_TVR']) == 1 || $data['KB2_TVR'] == ""){ $tvrFlag = 1; }
            if(strlen($data['KB2_AIP']) == 1 || $data['KB2_AIP'] == ""){ $aipFlag = 1; }
        }

        if($bitMapFlag == 0 || $userFOFlag == 0 || $arqcFlag == 0 || $amtAuthFlag == 0 ||
        $amtOtherFlag == 0 || $atcFlag == 0 || $termConFlag == 0 || $termCurrFlag == 0 ||
        $transDateFlag == 0 || $transTypeFlag == 0 || $umpNumFlag == 0 || $tvrFlag == 0 ||
        $appDataLenFlag == 0 || $appDataFlag == 0 || $cryptoFlag == 0 || $aipFlag == 0){
            $answer[$key] = new stdClass();
            $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> ID_Code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> ID_Entry_Mode = $data['ENTRY_MODE'];
            $answer[$key] -> Bit_Map = $data['KB2_BIT_MAP'];
            $answer[$key] -> bitMapFlag = $bitMapFlag;
            $answer[$key] -> User_Field_One = $data['KB2_USR_FLD1'];
            $answer[$key] -> userFOFlag = $userFOFlag;
            $answer[$key] -> Crypto_Data = $data['KB2_CRYPTO_INFO_DATA'];
            $answer[$key] -> cryptoFlag = $cryptoFlag;
            $answer[$key] -> ARQC = $data['KB2_ARQC'];
            $answer[$key] -> arqcFlag = $arqcFlag;
            $answer[$key] -> AMT_Auth = $data['KB2_AMT_AUTH'];
            $answer[$key] -> amtAuthFlag = $amtAuthFlag;
            $answer[$key] -> AMT_Other = $data['KB2_AMT_OTHER'];
            $answer[$key] -> amtOtherFlag = $amtOtherFlag;
            $answer[$key] -> ATC = $data['KB2_ATC'];
            $answer[$key] -> atcFlag = $atcFlag;
            $answer[$key] -> Terminal_Country_Code = $data['KB2_TERM_CTRY_CDE'];
            $answer[$key] -> termConFlag = $termConFlag;
            $answer[$key] -> Terminal_Currency_Code = $data['KB2_TRAN_CRNCY_CDE'];
            $answer[$key] -> termCurrFlag = $termCurrFlag;
            $answer[$key] -> Transaction_Date = $data['KB2_TRAN_DAT'];
            $answer[$key] -> transDateFlag = $transDateFlag;
            $answer[$key] -> Transaction_Type = $data['KB2_TRAN_TYPE'];
            $answer[$key] -> transTypeFlag = $transTypeFlag; 
            $answer[$key] -> Umpedict_Number = $data['KB2_UNPREDICT_NUM'];
            $answer[$key] -> umpNumFlag = $umpNumFlag;
            $answer[$key] -> Issuing_App_Data_Length = $data['KB2_ISS_APPL_DATA_LGTH'];
            $answer[$key] -> appDataLenFlag = $appDataLenFlag;
            $answer[$key] -> Issuing_App_Data = $data['KB2_ISS_APPL_DATA'];
            $answer[$key] -> appDataFlag = $appDataFlag;
            $answer[$key] -> TVR = $data['KB2_TVR'];
            $answer[$key] -> tvrFlag = $tvrFlag;
            $answer[$key] -> AIP = $data['KB2_AIP'];
            $answer[$key] -> aipFlag = $aipFlag;
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
        }else{
            $answerOk[$key] = new stdClass();
            $answerOk[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answerOk[$key] -> ID_Code_Response = $data['CODIGO_RESPUESTA'];
            $answerOk[$key] -> ID_Entry_Mode = $data['ENTRY_MODE'];
            $answerOk[$key] -> Bit_Map = $data['KB2_BIT_MAP'];
            $answerOk[$key] -> bitMapFlag = $bitMapFlag;
            $answerOk[$key] -> User_Field_One = $data['KB2_USR_FLD1'];
            $answerOk[$key] -> userFOFlag = $userFOFlag;
            $answerOk[$key] -> Crypto_Data = $data['KB2_CRYPTO_INFO_DATA'];
            $answerOk[$key] -> cryptoFlag = $cryptoFlag;
            $answerOk[$key] -> ARQC = $data['KB2_ARQC'];
            $answerOk[$key] -> arqcFlag = $arqcFlag;
            $answerOk[$key] -> AMT_Auth = $data['KB2_AMT_AUTH'];
            $answerOk[$key] -> amtAuthFlag = $amtAuthFlag;
            $answerOk[$key] -> AMT_Other = $data['KB2_AMT_OTHER'];
            $answerOk[$key] -> amtOtherFlag = $amtOtherFlag;
            $answerOk[$key] -> ATC = $data['KB2_ATC'];
            $answerOk[$key] -> atcFlag = $atcFlag;
            $answerOk[$key] -> Terminal_Country_Code = $data['KB2_TERM_CTRY_CDE'];
            $answerOk[$key] -> termConFlag = $termConFlag;
            $answerOk[$key] -> Terminal_Currency_Code = $data['KB2_TRAN_CRNCY_CDE'];
            $answerOk[$key] -> termCurrFlag = $termCurrFlag;
            $answerOk[$key] -> Transaction_Date = $data['KB2_TRAN_DAT'];
            $answerOk[$key] -> transDateFlag = $transDateFlag;
            $answerOk[$key] -> Transaction_Type = $data['KB2_TRAN_TYPE'];
            $answerOk[$key] -> transTypeFlag = $transTypeFlag; 
            $answerOk[$key] -> Umpedict_Number = $data['KB2_UNPREDICT_NUM'];
            $answerOk[$key] -> umpNumFlag = $umpNumFlag;
            $answerOk[$key] -> Issuing_App_Data_Length = $data['KB2_ISS_APPL_DATA_LGTH'];
            $answerOk[$key] -> appDataLenFlag = $appDataLenFlag;
            $answerOk[$key] -> Issuing_App_Data = $data['KB2_ISS_APPL_DATA'];
            $answerOk[$key] -> appDataFlag = $appDataFlag;
            $answerOk[$key] -> TVR = $data['KB2_TVR'];
            $answerOk[$key] -> tvrFlag = $tvrFlag;
            $answerOk[$key] -> AIP = $data['KB2_AIP'];
            $answerOk[$key] -> aipFlag = $aipFlag;
            $answerOk[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $answerOk[$key] -> Number_Sec = $data['NUM_SEC'];
            //Separación de la cifra decimal y entero del monto
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $answerOk[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
            $answerOk[$key] -> ID_Comer = $data['ID_COMER'];
            $answerOk[$key] -> Term_Comer = $data['TERM_COMER'];
            $answerOk[$key] -> Fiid_Comer = $data['FIID_COMER'];
            $answerOk[$key] -> Fiid_Term = $data['FIID_TERM'];
            $answerOk[$key] -> Ln_Comer = $data['LN_COMER'];
            $answerOk[$key] -> Ln_Term = $data['LN_TERM'];
            $answerOk[$key] -> Fiid_Card = $data['FIID_TARJ'];
            $answerOk[$key] -> Ln_Card = $data['LN_TARJ'];
        }
        
    }
    $badResponse = array_values($answer);
    $goodResponse = array_values($answerOk);
    $generalResponse = array_merge($badResponse, $goodResponse);
    $arrayJson = json_decode(json_encode($generalResponse), true);
    return $arrayJson;
    }
}
