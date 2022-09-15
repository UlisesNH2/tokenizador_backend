<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class TokenC4Controller extends Controller
{
    public function index(Request $request)
    {
        $kq2 = $request -> Kq2;
        $codeResponse = $request -> Code_Response;
        $entryMode = $request -> Entry_Mode;
        $response = array();
        $answer = array();
        $array = array();
        $numberFilters = 0;
        $flagkq2 = false;
        $flagCode = false;
        $flagEntry = false;
        $query = "select KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
        KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
        KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD from test where ";

        /*
        Detectar cual de lso filtros está siendo utilizad.
        Se incermenta la variable $numberFilters para ingresar al switch
        y se configura la bandera para saber cual de estos filtros son utilizados.
        */
        if(!empty($kq2)){ $numberFilters++; $flagkq2 = true;}
        if(!empty($codeResponse)) { $numberFilters++; $flagCode = true;}
        if(!empty($entryMode)){ $numberFilters++; $flagEntry = true;}

        switch($numberFilters){
            case 1: { //Un solo filtro utilizado
                if($flagkq2){ //Filtrado por medio de Acceso
                    for($i = 0; $i < count($kq2); $i++){
                        $response = array_merge($response, DB::select($query."
                        KQ2_ID_MEDIO_ACCESO = ?", [$kq2[$i]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                if($flagCode){//Filtrado por código de respuesta
                    for($i = 0; $i < count($codeResponse); $i++){
                        $response = array_merge($response, DB::select($query."
                        CODIGO_RESPUESTA = ?", [$codeResponse[$i]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                if($flagEntry){ //Filtrado por entry mode
                    for($i = 0; $i < count($entryMode); $i++){
                        $response = array_merge($response, DB::select($query."
                        ENTRY_MODE = ?", [$entryMode[$i]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                break;
            }
            case 2: { //Dos filtros utilizados
                if($flagkq2){
                    if($flagCode && !$flagEntry){
                        $firstLength = max($kq2, $codeResponse);
                        switch($firstLength){
                            case $kq2: {
                                for($i = 0; $i < count($kq2); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        $response = array_merge($response, DB::select($query."
                                        KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?",
                                        [$kq2[$i], $codeResponse[$j]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                            case $codeResponse: {
                                for($i = 0; $i < count($codeResponse); $i++){
                                    for($j = 0; $j < count($kq2); $j++){
                                        $response = array_merge($response, DB::select($query."
                                        KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?",
                                        [$kq2[$j], $codeResponse[$i]]));
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
                                            $response = array_merge($response, DB::select($query."
                                            KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?",
                                            [$kq2[$i], $entryMode[$j]]));
                                        }
                                    }
                                    $array = json_decode(json_encode($response), true);
                                    break;
                                }
                                case $entryMode: {
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            $response = array_merge($response, DB::select($query."
                                            KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?",
                                            [$kq2[$j], $entryMode[$i]]));
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
                                        $response = array_merge($response, DB::select($query."
                                        CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", 
                                        [$codeResponse[$i], $entryMode[$j]]));
                                    }
                                }
                                $array = json_decode(json_encode($response), true);
                                break;
                            }
                            case $entryMode: {
                                for($i = 0; $i < count($entryMode); $i++){
                                    for($j = 0; $j < count($codeResponse); $j++){
                                        $response = array_merge($response, DB::select($query."
                                        CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", 
                                        [$codeResponse[$j], $entryMode[$i]]));
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
            case 3: { //Los tres filtros son utilizados
                if($flagkq2 && $flagCode && $flagEntry){
                    $firstLength = max($kq2, $codeResponse, $entryMode);
                    switch($firstLength){
                        case $kq2:{ //Medio de Acceso (filtro mas largo)
                            $secondLength = max($codeResponse, $entryMode);
                            switch($secondLength){
                                case $codeResponse:{
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($codeResponse); $j++){
                                            for($z = 0; $z < count($entryMode); $z++){
                                                $response = array_merge($response, DB::select($query."
                                                KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$i], $codeResponse[$j], $entryMode[$z]]));
                                            }
                                        }
                                    }
                                    break;
                                }
                                case $entryMode: {
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            for($z = 0; $z < count($codeResponse); $z++){
                                                $response = array_merge($response, DB::select($query."
                                                KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$i], $codeResponse[$z], $entryMode[$j]]));
                                            }
                                        }
                                    }
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                            break;
                        }
                        case $codeResponse:{ //Código de respuesta (filtro más largo)
                            $secondLength = max($kq2, $entryMode);
                            switch($secondLength){
                                case $kq2:{
                                    for($i = 0; $i < count($codeResponse); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            for($z = 0; $z < count($entryMode); $z++){
                                                $response = array_merge($response, DB::select($query."
                                                KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
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
                                                $response = array_merge($response, DB::select($query."
                                                KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$z], $codeResponse[$i], $entryMode[$j]]));
                                            }
                                        }
                                    }
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                            break;
                        }
                        case $entryMode:{//Entry mode (filtro más largo)
                            $secondLength = max($kq2, $codeResponse);
                            switch($secondLength){
                                case $kq2: {
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            for($z = 0; $z < count($codeResponse); $z++){
                                                $response = array_merge($response, DB::select($query."
                                                KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
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
                                                $response = array_merge($response, DB::select($query."
                                                KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?",
                                                [$kq2[$z], $codeResponse[$j], $entryMode[$z]]));
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
                break;
            }
            default: {
                $response = DB::select("select KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
                KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
                KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD from test");
                $array = json_decode(json_encode($response), true);
                break;
            }
        }

        foreach($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID_Terminal_Attended = $data['KC4_TERM_ATTEND_IND'];
            $answer[$key]->ID_Terminal = $data['KC4_TERM_OPER_IND'];
            $answer[$key]->Terminal_Location = $data['KC4_TERM_LOC_IND'];
            $answer[$key]->ID_Cardholder_Presence = $data['KC4_CRDHLDR_PRESENT_IND'];
            $answer[$key]->ID_Card_Presence = $data['KC4_CRD_PRESENT_IND'];
            $answer[$key]->ID_Card_Capture = $data['KC4_CRD_CAPTR_IND'];
            $answer[$key]->ID_Status = $data['KC4_TXN_STAT_IND'];
            $answer[$key]->Security_Level = $data['KC4_TXN_SEC_IND'];
            $answer[$key]->Routing_Indicator = $data['KC4_TXN_RTN_IND'];
            $answer[$key]->Terminal_Activation_Cardholder = $data['KC4_CRDHLDR_ACTVT_TERM_IND'];
            $answer[$key]->ID_Terminal_Data_Transfer = $data['KC4_TERM_INPUT_CAP_IND'];
            $answer[$key]->ID_Cardholder_Method = $data['KC4_CRDHLDR_ID_METHOD'];
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }

    //FUNCIÓN PARA MANDAR INFORMACIÓN A LA TABLAC4 (FILTRADA)
    public function getTableFilter(Request $request)
    {
        $values = array();
        $valuesDate = array();
        $label = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'KC4_TERM_ATTEND_IND', 'KC4_TERM_OPER_IND', 'KC4_TERM_LOC_IND', 
        'KC4_CRDHLDR_PRESENT_IND','KC4_CRD_PRESENT_IND', 'KC4_CRD_CAPTR_IND', 'KC4_TXN_STAT_IND', 'KC4_TXN_SEC_IND', 'KC4_TXN_RTN_IND',
        'KC4_CRDHLDR_ACTVT_TERM_IND', 'KC4_TERM_INPUT_CAP_IND', 'KC4_CRDHLDR_ID_METHOD', 'ID_COMER', 'TERM_COMER', 'FIID_COMER', 'FIID_TERM',
        'LN_COMER', 'LN_TERM', 'FIID_TARJ', 'LN_TARJ'];

        $values[0] = $request->Kq2;
        $values[1] = $request->Code_Response;
        $values[2] = $request->Entry_Mode;
        $values[3] = $request->ID_Terminal_Attended;
        $values[4] = $request->ID_Terminal;
        $values[5] = $request->Terminal_Location;
        $values[6] = $request->ID_Cardholder_Presence;
        $values[7] = $request->ID_Card_Presence;
        $values[8] = $request->ID_Card_Capture;
        $values[9] = $request->ID_Status;
        $values[10] = $request->Security_Level;
        $values[11] = $request->Routing_Indicator;
        $values[12] = $request->Terminal_Activation_Cardholder;
        $values[13] = $request->ID_Terminal_Data_Transfer;
        $values[14] = $request->ID_Cardholder_Method;
        $values[15] = $request->ID_Comer;
        $values[16] = $request->Term_Comer;
        $values[17] = $request->Fiid_Comer;
        $values[18] = $request->Fiid_Term;
        $values[19] = $request->Ln_Comer;
        $values[20] = $request->Ln_Term;
        $values[21] = $request->Fiid_Card;
        $values[22] = $request->Ln_Card;

        $valuesDate[0] = $request -> startDate;
        $valuesDate[1] = $request -> finishDate;
        $valuesDate[2] = $request -> startHour;
        $valuesDate[3] = $request -> finishHour; 

        $arrayValues = array();

        $answer = array();
        $response = array();
        $array = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
        KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
        KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 from test where (FECHA_TRANS >= ? and FECHA_TRANS <= ?) and 
        (HORA_TRANS >= ? and HORA_TRANS <= ?) and ";

        $queryOutFilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
        KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
        KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC, MONTO1 from test where (FECHA_TRANS >= ? and FECHA_TRANS <= ?) and 
        (HORA_TRANS >= ? and HORA_TRANS <= ?)";

        $queryDateTime = " and (FECHA_TRANS >= ? and FECHA_TRANS <= ?) and (HORA_TRANS >= ? and HORA_TRANS <= ?)";

        //Eliminar values y label que no se estén filtrando
        for ($key = 0; $key < 23; $key++) {
            if(empty($values[$key])) {
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
            $response = DB::select($queryOutFilters, [...$valuesDate]);
            $array = json_decode(json_encode($response), true);
        }else {
            //Ingresar todos los valores elegidos en el filtro dentro de un solo arreglo. (Valores para la consulta)
            for ($i = 0; $i < count($filteredValues); $i++) {
                for ($j = 0; $j < count($filteredValues[$i]); $j++) {
                    array_push($arrayValues, $filteredValues[$i][$j]);
                }
            }
            $z = 1; //Variable 'controladora' de el largo del query
            //Constructor del query (Varias consultas al mismo tiempo)
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
            //Consulta del query obtenido por los filtros y los valores elegidos
            $response = DB::select($query.$queryDateTime, [...$arrayValues, ...$valuesDate]);
            $array = json_decode(json_encode($response), true);
        }
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key]->kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key]->codeResp = $data['CODIGO_RESPUESTA'];
            $answer[$key]->entryMode = $data['ENTRY_MODE'];
            $answer[$key]->idTermAt = $data['KC4_TERM_ATTEND_IND']; //subcampo 1
            $answer[$key]->idTerm = $data['KC4_TERM_OPER_IND']; //subcampo 2
            $answer[$key]->termLoc = $data['KC4_TERM_LOC_IND']; //subcampo 3
            $answer[$key]->chPres = $data['KC4_CRDHLDR_PRESENT_IND']; //subcampo 4
            $answer[$key]->cardPres = $data['KC4_CRD_PRESENT_IND']; //subcampo 5
            $answer[$key]->cardCap = $data['KC4_CRD_CAPTR_IND']; //subcampo 6
            $answer[$key]->status = $data['KC4_TXN_STAT_IND']; //subcampo 7
            $answer[$key]->secLevel = $data['KC4_TXN_SEC_IND']; //subcampo 8
            $answer[$key]->idRouting = $data['KC4_TXN_RTN_IND']; //subcampo 9
            $answer[$key]->termActCh = $data['KC4_CRDHLDR_ACTVT_TERM_IND']; //subcampo 10
            $answer[$key]->termDataTrans = $data['KC4_TERM_INPUT_CAP_IND']; //subcampo 11
            $answer[$key]->chMeth = $data['KC4_CRDHLDR_ID_METHOD']; // subcampo 12
            $answer[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $answer[$key]->Number_Sec = $data['NUM_SEC'];
            //Separación del monto en decimal y entero para agregar el punto decimal
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) - 2, 2);
            $int =  substr($data['MONTO1'], 0, strlen($data['MONTO1']) - 2);
            $answer[$key]->amount = '$' . number_format($int . '.' . $dec, 2);
            $answer[$key]->ID_Comer = $data['ID_COMER'];
            $answer[$key]->Term_Comer = $data['TERM_COMER'];
            $answer[$key]->Fiid_Comer = $data['FIID_COMER'];
            $answer[$key]->Fiid_Term = $data['FIID_TERM'];
            $answer[$key]->Ln_Comer = $data['LN_COMER'];
            $answer[$key]->Ln_Term = $data['LN_TERM'];
            $answer[$key]->Fiid_Card = $data['FIID_TARJ'];
            $answer[$key]->Ln_Card = $data['LN_TARJ'];
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }
}
