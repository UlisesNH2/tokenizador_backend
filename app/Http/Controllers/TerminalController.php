<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;
use Illuminate\Support\Facades\DB;

class TerminalController extends Controller
{
    public function index(Request $request){
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
        $queryFIID_COMER = "select main.FIID_COMER, catComer.FIID_COMER_DES from test as main
        join fiid_comer as catComer on main.FIID_COMER = catComer.FIID_COMER";

        $queryFIID_TARJ = "select main.FIID_TARJ, catTarj.FIID_TARJ_DES from test as main 
        join fiid_tarj as catTarj on main.FIID_TARJ = catTarj.FIID_TARJ";

        $queryFIID_TERM = "select main.FIID_TERM, catComer.FIID_COMER_DES from test as main 
        join fiid_comer as catComer on main.FIID_COMER = catComer.FIID_COMER";

        $queryLN_COMER = "select main.LN_COMER, catLNComer.LN_COMER_DES from test as main 
        join ln_comer as catLNComer on main.LN_COMER = catLNComer.LN_COMER";

        $queryLN_TERM = "select main.LN_TERM, catLNComer.LN_COMER_DES from test as main
        join ln_comer as catLNComer on main.LN_COMER = catLNComer.LN_COMER";

        /*
        Detectar cual de lso filtros está siendo utilizad.
        Se incermenta la variable $numberFilters para ingresar al switch
        y se configura la bandera para saber cual de estos filtros son utilizados.
        */
        if(!empty($kq2)){ $numberFilters++; $flagkq2 = true;}
        if(!empty($codeResponse)) { $numberFilters++; $flagCode = true;}
        if(!empty($entryMode)){ $numberFilters++; $flagEntry = true;}

        switch($numberFilters){
            /*
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
            */
            default: {
                $z = 0; //Variable para controlar las asignaciones de stdClass para la creación 
                //de objetos dentro del arreglo 'answer' 
                //Consulta para FIID_COMER
                $responseSub = DB::select($queryFIID_COMER);
                $array = json_decode(json_encode($responseSub), true);
                $arrayClened = array_values(array_unique($array, SORT_REGULAR)); //Quitar valores repetidos
                foreach($arrayClened as $key => $data){
                    $answer[$z] = new stdClass();
                    $answer[$z] -> Fiid_Comer = $data['FIID_COMER'];
                    $answer[$z] -> Fiid_Comer_Des = $data['FIID_COMER_DES'];
                    $z++;
                }
                //Consulta para FIID_TERM
                $responseSub = DB::select($queryFIID_TERM);
                $array = json_decode(json_encode($responseSub), true);
                $arrayClened = array_values(array_unique($array, SORT_REGULAR));
                foreach($arrayClened as $key => $data){
                    $answer[$z] = new stdClass();
                    $answer[$z] -> Fiid_Term = $data['FIID_TERM'];
                    $answer[$z] -> Fiid_Term_Des = $data['FIID_COMER_DES'];
                    $z++;
                }
                //Consulta para FIID_TARJ
                $responseSub = DB::select($queryFIID_TARJ);
                $array = json_decode(json_encode($responseSub), true);
                $arrayClened = array_values(array_unique($array, SORT_REGULAR));
                foreach($arrayClened as $key => $data){
                    $answer[$z] = new stdClass();
                    $answer[$z] -> Fiid_Tarj = $data['FIID_TARJ'];
                    $answer[$z] -> Fiid_Tarj_Des = $data['FIID_TARJ_DES'];
                    $z++;
                }
                //Consulta para LN_COMER
                $responseSub = DB::select($queryLN_COMER);
                $array = json_decode(json_encode($responseSub), true);
                $arrayClened = array_values(array_unique($array, SORT_REGULAR));
                foreach($arrayClened as $key => $data){
                    $answer[$z] = new stdClass();
                    $answer[$z] -> Ln_Comer = $data['LN_COMER'];
                    $answer[$z] -> Ln_Comer_Des = $data['LN_COMER_DES'];
                }
                //Consulta para LN_TERM
                $responseSub = DB::select($queryLN_TERM);
                $array = json_decode(json_encode($responseSub), true);
                $arrayClened = array_values(array_unique($array, SORT_REGULAR));
                foreach($arrayClened as $key => $data){
                    $answer[$z] = new stdClass();
                    $answer[$z] -> Ln_Term = $data['LN_TERM'];
                    $answer[$z] -> Ln_Term_Des = $data['LN_COMER_DES'];
                }
                break;
            }
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }
}
