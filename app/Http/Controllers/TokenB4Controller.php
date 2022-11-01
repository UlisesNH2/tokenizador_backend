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
    public function index(Request $request)
    {
        $kq2 = $request -> Kq2;
        $codeResponse = $request -> Code_Response;
        $entryMode = $request -> Entry_Mode;
        $numberFilters = 0;
        $flagkq2 = false;
        $flagCode = false;
        $flagEntry = false;
        $response = array();
        $query = "select KB4_PT_SRV_ENTRY_MDE, KB4_TERM_ENTRY_CAP, KB4_LAST_EMV_STAT, KB4_DATA_SUSPECT,
        KB4_APPL_PAN_SEQ_NUM, KB4_DEV_INFO, KB4_RSN_ONL_CDE, KB4_ARQC_VRFY, KB4_ISO_RC_IND from ".$request -> bd." where ";

        //Detectar cuales son los filtros que estan siendo utilizados
        if(!empty($kq2)){ $numberFilters++; $flagkq2 = true; }
        if(!empty($codeResponse)){ $numberFilters++; $flagCode = true; }
        if(!empty($entryMode)){ $numberFilters++; $flagEntry = true; }

        switch($numberFilters){
            case 1:{
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
            case 2:{ //Dos filtros utilizados
                if($flagkq2){//Entry mode
                    if($flagCode && !$flagEntry){ //Código de respuesta
                        $firstLength = max($kq2, $codeResponse);
                        switch($firstLength){
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
                            case $codeResponse: {
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
                            $firstLength = max($kq2, $entryMode);
                            switch($firstLength){
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
                        $firstLength = max($codeResponse, $entryMode);
                        switch($firstLength){
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
            case 3:{ //Tres filtros utilizados
                if($flagkq2 && $flagCode && $flagEntry){
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
                                    break;
                                }
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
            default: {
                $response = DB::select("select KB4_PT_SRV_ENTRY_MDE, KB4_TERM_ENTRY_CAP, KB4_LAST_EMV_STAT, KB4_DATA_SUSPECT,
                KB4_APPL_PAN_SEQ_NUM, KB4_DEV_INFO, KB4_RSN_ONL_CDE, KB4_ARQC_VRFY, KB4_ISO_RC_IND from ".$request -> bd);
                $array = json_decode(json_encode($response), true);
            }
        }
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> Service_EntryMode = $data['KB4_PT_SRV_ENTRY_MDE'];
            $answer[$key] -> Capacity_Terminal = $data['KB4_TERM_ENTRY_CAP'];
            $answer[$key] -> EVM_Status = $data['KB4_LAST_EMV_STAT'];
            $answer[$key] -> Data_Suspect = $data['KB4_DATA_SUSPECT'];
            $answer[$key] -> PAN_Number = $data['KB4_APPL_PAN_SEQ_NUM'];
            $answer[$key] -> Device_Info = $data['KB4_DEV_INFO'];
            $answer[$key] -> Online_Code = $data['KB4_RSN_ONL_CDE'];
            $answer[$key] -> ARQC_Verification = $data['KB4_ARQC_VRFY'];
            $answer[$key] -> ID_Response_ISO = $data['KB4_ISO_RC_IND'];
        }
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }

    public function getDataTableFilter(Request $request){

        $values = array();
        $valuesDate = array();
        $labels = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE','KB4_PT_SRV_ENTRY_MDE', 'KB4_TERM_ENTRY_CAP', 'KB4_LAST_EMV_STAT', 'KB4_DATA_SUSPECT', 
        'KB4_APPL_PAN_SEQ_NUM', 'KB4_DEV_INFO', 'KB4_RSN_ONL_CDE', 'KB4_ARQC_VRFY', 'KB4_ISO_RC_IND', 'ID_COMER', 'TERM_COMER', 'FIID_COMER', 'FIID_TERM',
        'LN_COMER', 'LN_TERM', 'FIID_TARJ', 'LN_TARJ'];

        $values[0] = $request -> Kq2;
        $values[1] = $request -> Code_Response;
        $values[2] = $request -> Entry_Mode;
        $values[3] = $request -> Service_EntryMode;
        $values[4] = $request -> Capacity_Terminal;
        $values[5] = $request -> EVM_Status;
        $values[6] = $request -> Data_Suspect;
        $values[7] = $request -> PAN_Number;
        $values[8] = $request -> Device_Info;
        $values[9] = $request -> Online_Code;
        $values[10] = $request -> ARQC_Verification;
        $values[11] = $request -> ID_Response_ISO;
        $values[12] = $request -> ID_Comer;
        $values[13] = $request -> Term_Comer;
        $values[14] = $request -> Fiid_Comer;
        $values[15] = $request -> Fiid_Term;
        $values[16] = $request -> Ln_Comer;
        $values[17] = $request -> Ln_Term;
        $values[18] = $request -> Fiid_Card;
        $values[19] = $request -> Ln_Card;

        $valuesDate[0] = $request -> startDate;
        $valuesDate[1] = $request -> finishDate;
        $valuesDate[2] = $request -> startHour;
        $valuesDate[3] = $request -> finishHour;

        $answer = array();
        $array = array();
        $response = array();
        $arrayValues = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB4_PT_SRV_ENTRY_MDE, KB4_TERM_ENTRY_CAP, KB4_LAST_EMV_STAT, KB4_DATA_SUSPECT,
        KB4_APPL_PAN_SEQ_NUM, KB4_DEV_INFO, KB4_RSN_ONL_CDE, KB4_ARQC_VRFY, KB4_ISO_RC_IND, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1, TIPO from ".$request -> bd." where ";

        $queryOutFilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KB4_PT_SRV_ENTRY_MDE, KB4_TERM_ENTRY_CAP, KB4_LAST_EMV_STAT, KB4_DATA_SUSPECT,
        KB4_APPL_PAN_SEQ_NUM, KB4_DEV_INFO, KB4_RSN_ONL_CDE, KB4_ARQC_VRFY, KB4_ISO_RC_IND, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1, TIPO from ".$request -> bd." where (FECHA_TRANS >= ? and FECHA_TRANS <= ?) and 
        (HORA_TRANS >= ? and HORA_TRANS <= ?)";

        $queryDateTime = " and (FECHA_TRANS >= ? and FECHA_TRANS <= ?) and (HORA_TRANS >= ? and HORA_TRANS <= ?)";

        //Detectar cuales son los filtros utilizados
        for($key = 0; $key < 20; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);
        
        for($i = 0; $i < count($filteredValues); $i++){
            for($j = 0; $j < count($filteredValues[$i]); $j++){
                if($filteredValues[$i][$j] === null){
                    $filteredValues[$i][$j] = " ";
                }
            }
        }

        if(empty($filteredValues)){
            $response = DB::select($queryOutFilters, [...$valuesDate]);
            $array = json_decode(json_encode($response), true);
        }else{
            //Ingresar todos los $filteredValues en un solo arreglo
            for ($i = 0; $i < count($filteredValues); $i++) {
                for ($j = 0; $j < count($filteredValues[$i]); $j++) {
                    array_push($arrayValues, $filteredValues[$i][$j]);
                }
            }
            $z = 1; //Variable para el control del query
            //Construcción del query varias consultas al mismo tiempo
            for ($i = 0; $i < count($filteredValues); $i++) {
                for ($j = 0; $j < count($filteredValues[$i]); $j++) {
                    if ($j == count($filteredValues[$i]) - 1) {
                        if ($j == 0) {
                            if ($z == count($arrayValues)) {
                                $query .= "(" . $filteredLabels[$i] . " = ?)";
                            } else {
                                $query .= "(" . $filteredLabels[$i] . " = ?) and ";
                            }
                            $z++;
                        } else {
                            if ($z == count($arrayValues)) {
                                $query .= $filteredLabels[$i] . " = ?)";
                                $z = 1;
                            } else {
                                $query .= $filteredLabels[$i] . " = ?) and ";
                                $z++;
                            }
                        }
                    } else {
                        if ($j == 0) {
                            $query .= "(" . $filteredLabels[$i] . " = ? or ";
                            $z++;
                        } else {
                            $query .= $filteredLabels[$i] . " = ? or ";
                            $z++;
                        }
                    }
                }
            }
            $response = DB::select($query . $queryDateTime, [...$arrayValues, ...$valuesDate]);
            $array = json_decode(json_encode($response), true);
        }
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key]->kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key]->codeResp = $data['CODIGO_RESPUESTA'];
            $answer[$key]->entryMode = $data['ENTRY_MODE'];
            $answer[$key]->SerEntryMode = $data['KB4_PT_SRV_ENTRY_MDE'];
            $answer[$key]->CapTerm = $data['KB4_TERM_ENTRY_CAP'];
            $answer[$key]->EVMSts = $data['KB4_LAST_EMV_STAT'];
            $answer[$key]->DataSus = $data['KB4_DATA_SUSPECT'];
            $answer[$key]->PANum = $data['KB4_APPL_PAN_SEQ_NUM'];
            $answer[$key]->DevInfo = $data['KB4_DEV_INFO'];
            $answer[$key]->OnlCode = $data['KB4_RSN_ONL_CDE'];
            $answer[$key]->ARQCVer = $data['KB4_ARQC_VRFY'];
            $answer[$key]->RespISO = $data['KB4_ISO_RC_IND'];
            $answer[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $answer[$key]->Number_Sec = $data['NUM_SEC'];
            //Separación de la cifra decimal y entero del monto
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) - 2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) - 2);
            $answer[$key]->amount = '$' . number_format($int . '.' . $dec, 2);
            $answer[$key]->type = $data['TIPO'];
            $answer[$key]->ID_Comer = $data['ID_COMER'];
            $answer[$key]->Term_Comer = $data['TERM_COMER'];
            $answer[$key]->Fiid_Comer = $data['FIID_COMER'];
            $answer[$key]->Fiid_Term = $data['FIID_TERM'];
            $answer[$key]->Ln_Comer = $data['LN_COMER'];
            $answer[$key]->Ln_Term = $data['LN_TERM'];
            $answer[$key]->Fiid_Card = $data['FIID_TARJ'];
            $answer[$key]->Ln_Card = $data['LN_TARJ'];
        }
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }
}
