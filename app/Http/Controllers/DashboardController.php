<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $kq2 = $request -> kq2;
        $code_Response = $request -> codeResponse;
        $entry_Mode = $request -> entryMode;
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, sum(MONTO1) AS MONTO, 
        count(*) as TXS from test group by CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO, ENTRY_MODE having ";
        $response = array();
        $array = array();
        $answer = array();
        $numberFilters = 0;
        $flagkq2 = false;
        $flagCode = false;
        $flagEntryMode = false;

        //Identificar cuantos filtros se han utilizado
        if(!empty($kq2)) { $numberFilters++; $flagkq2 = true;}
        if(!empty($code_Response)) { $numberFilters++; $flagCode = true;}
        if(!empty($entry_Mode)) { $numberFilters++; $flagEntryMode = true;}

        switch($numberFilters){
            //Un solo filtro
            case 1: {
                //Medio de Acceso
                if($flagkq2){
                    for($key = 0; $key < count($kq2); $key++){
                        //Array_merge -> Juntar todos los arreglos obtenidos de la consulta en un solo arreglo de objetos
                        $response = array_merge($response, DB::select($query."KQ2_ID_MEDIO_ACCESO = ?", [$kq2[$key]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                //Codigo de Respuesta
                if($flagCode){
                    for($key = 0; $key < count($code_Response); $key++){
                        $response = array_merge($response, DB::select($query."CODIGO_RESPUESTA = ? ", [$code_Response[$key]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                //Entry Mode
                if($flagEntryMode){
                    for($key = 0; $key < count($entry_Mode); $key++){
                        $response = array_merge($response, DB::select($query."ENTRY_MODE = ?", [$entry_Mode[$key]]));
                    }
                    $array = json_decode(json_encode($response), true);
                }
                break;
            }
            //Dos filtros
            case 2: {
                //VALIDACIÓN PARA PRIMER FILTRO
                if($flagkq2){ //MEDIO DE ACCESO
                    if($flagCode && !$flagEntryMode){ //CODIGO DE RESPUESTA (SEGUNDO FILTRO)
                        //Comparación de longitudes de los arreglos 
                        if(count($kq2) < count($code_Response)){ //Filtro KQ2 es menor que CODIGO DE RESPUESTA
                            $subqueryKQ2 = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, sum(MONTO1) AS MONTO, 
                            count(*) as TXS from test group by CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO, ENTRY_MODE having 
                            KQ2_ID_MEDIO_ACCESO = ? ";
                            for($i = 0; $i < count($kq2); $i++){
                                for($j = 0; $j < count($code_Response); $j++){
                                    $response = array_merge($response, DB::select($subqueryKQ2."
                                    and CODIGO_RESPUESTA = ?", [$kq2[$i], $code_Response[$j]]));
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                        }
                        if(count($kq2) > count($code_Response)){
                            $subqueryCode = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, sum(MONTO1) AS MONTO, 
                            count(*) as TXS from test group by CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO, ENTRY_MODE having 
                            CODIGO_RESPUESTA = ? ";
                            for($i = 0; $i < count($code_Response); $i++){
                                for($j = 0; $j < count($kq2); $j++){
                                    $response = array_merge($response, DB::select($subqueryCode."
                                    and KQ2_ID_MEDIO_ACCESO = ?", [$code_Response[$i], $kq2[$j]]));
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                        }
                        if(count($kq2) == count($code_Response)){
                            for($i = 0; $i < count($kq2); $i++){
                                $response = array_merge($response, DB::select($query."
                                KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ?", [$kq2[$i], $code_Response[$i]]));
                            }
                            $array = json_decode(json_encode($response), true); 
                        }
                    }else{ //ENTRY MODE (SEGUNDO FILTRO)
                        if(count($kq2) < count($entry_Mode)){
                            $subqueryKQ2 = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, sum(MONTO1) AS MONTO, 
                            count(*) as TXS from test group by CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO, ENTRY_MODE having 
                            KQ2_ID_MEDIO_ACCESO = ?";
                            for($i = 0; $i < count($kq2); $i++){
                                for($j = 0; $j < count($entry_Mode); $j++){
                                    $response = array_merge($response, DB::select($subqueryKQ2."
                                    and ENTRY_MODE = ?", [$kq2[$i], $entry_Mode[$j]]));
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                        }
                        if(count($kq2) > count($entry_Mode)){
                            $subqueryEntry = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, sum(MONTO1) AS MONTO, 
                            count(*) as TXS from test group by CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO, ENTRY_MODE having 
                            ENTRY_MODE = ?";
                            for($i = 0; $i < count($entry_Mode); $i++){
                                for($j = 0; $j < count($kq2); $j++){
                                    $response = array_merge($response, DB::select($subqueryEntry."
                                    and KQ2_ID_MEDIO_ACCESO = ?", [$entry_Mode[$i], $kq2[$j]]));
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                        }
                        if(count($kq2) == count($entry_Mode)){
                            for($i = 0; $i < count($kq2); $i++){
                                $response = array_merge($response, DB::select($query."
                                KQ2_ID_MEDIO_ACCESO = ? and ENTRY_MODE = ?", [$kq2[$i], $entry_Mode[$i]]));
                            }
                            $array = json_decode(json_encode($response), true);
                        }
                    }
                }else{
                    if($flagCode && $flagEntryMode){ //CODIGO DE RESPUESTA y ENTRY MODE
                        if(count($code_Response) < count($entry_Mode)){
                            $subqueryCode = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, sum(MONTO1) AS MONTO, 
                            count(*) as TXS from test group by CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO, ENTRY_MODE having 
                            CODIGO_RESPUESTA = ? ";
                            for($i = 0; $i < count($code_Response); $i++){
                                for($j = 0; $j < count($code_Response); $j++){
                                    $response = array_merge($response, DB::select($subqueryCode."
                                    and ENTRY_MODE = ?", [$code_Response[$i], $entry_Mode[$j]]));
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                        }
                        if(count($code_Response) > count($entry_Mode)){
                            $subqueryEntry = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, sum(MONTO1) AS MONTO, 
                            count(*) as TXS from test group by CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO, ENTRY_MODE having 
                            ENTRY_MODE = ? ";
                            for($i = 0; $i < count($entry_Mode); $i++){
                                for($j = 0; $j < count($code_Response); $j++){
                                    $response = array_merge($response, DB::select($subqueryEntry."
                                    and CODIGO_RESPUESTA = ?", [$entry_Mode[$i], $code_Response[$j]]));
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                        }
                        if(count($code_Response) == count($entry_Mode)){
                            for($i = 0; $i < count($code_Response); $i++){
                                $response = array_merge($response, DB::select($query."
                                CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", [$code_Response[$i], $entry_Mode[$i]]));
                            }
                            $array = json_decode(json_encode($response), true);
                        }
                    }
                }
                break;
            }
            //Tres filtros
            case 3:{
                //Confirmación de los tres filtros utilizados
                if($flagkq2 && $flagCode && $flagEntryMode){
                    //max() -> saber cual de los tres filtros (arreglos) es el que tiene más elementos.
                    //En caso de que todos sean iguales, retorna el elemento (valor) más grande de los tres filtros.
                    $firstLength = max($kq2, $code_Response, $entry_Mode); 
                    $subquery = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, sum(MONTO1) AS MONTO, 
                    count(*) as TXS from test group by CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO, ENTRY_MODE having 
                    KQ2_ID_MEDIO_ACCESO = ? and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?";
                    //Validación del filtro más largo.
                    switch($firstLength){
                        case $kq2:{//La variable '$firstLength' es igual (==) a el filtro (arreglo) $kq2
                            $secondLength = max($code_Response, $entry_Mode);
                            switch($secondLength){
                                case $code_Response:{
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($code_Response); $j++){
                                            for($z = 0; $z < count($entry_Mode); $z++){
                                                $response = array_merge($response, DB::select($subquery,
                                                [$kq2[$i], $code_Response[$j], $entry_Mode[$z]]));
                                            }
                                        }
                                    }
                                    break;
                                }
                                case $entry_Mode: {
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($entry_Mode); $j++){
                                            for($z = 0; $z < count($code_Response); $z++){
                                                $response = array_merge($response, DB::select($subquery, 
                                                [$kq2[$i], $code_Response[$z], $entry_Mode[$j]]));
                                            }
                                        }
                                    }
                                    break;
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                            break;
                        }
                        case $code_Response: {
                            $secondLength = max($kq2, $entry_Mode);
                            switch($secondLength){
                                case $kq2: {
                                    for($i = 0; $i < count($code_Response); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            for($z = 0; $z < count($entry_Mode); $z++){
                                                $response = array_merge($response, DB::select($subquery,
                                                [$kq2[$j], $code_Response[$i], $entry_Mode[$z]]));
                                            }
                                        }
                                    }
                                    break;
                                }
                                case $entry_Mode: {
                                    for($i = 0; $i < count($code_Response); $i++){
                                        for($j = 0; $j < count($entry_Mode); $j++){
                                            for($z = 0; $z < count($kq2); $z++){
                                                $response = array_merge($response, DB::select($subquery,
                                                [$kq2[$z], $code_Response[$i], $entry_Mode[$j]]));
                                            }
                                        }
                                    }
                                }
                            }
                            $array = json_decode(json_encode($response), true);
                            break;
                        }
                        case $entry_Mode: {
                            $secondLength = max($kq2, $code_Response);
                            switch($secondLength){
                                case $kq2:{
                                    for($i = 0; $i < count($entry_Mode); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            for($z = 0; $z < count($code_Response); $z++){
                                                $response = array_merge($response, DB::select($subquery,
                                                [$kq2[$j], $code_Response[$z], $entry_Mode[$i]]));
                                            }
                                        }
                                    }
                                    break;
                                }
                                case $code_Response:{
                                    for($i = 0; $i < count($entry_Mode); $i++){
                                        for($j = 0; $j < count($code_Response); $j++){
                                            for($z = 0; $z < count($kq2); $z++){
                                                $response = array_merge($response, DB::select($subquery,
                                                [$kq2[$z], $code_Response[$j], $entry_Mode[$i]]));
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
                $response = DB::select("select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, sum(MONTO1) AS MONTO, 
                count(*) as TXS from test group by CODIGO_RESPUESTA, KQ2_ID_MEDIO_ACCESO, ENTRY_MODE");
                $array = json_decode(json_encode($response), true);
                break;
            }
        }

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
            //Separación decimal y entero del monto para agregar el punto
            $dec = substr($data["MONTO"], strlen($data['MONTO'])-2, 2);
            $int = substr($data['MONTO'], 0, strlen($data['MONTO'])-2);
            $answer[$key] -> amount = $int.".".$dec;
            $answer[$key] -> tx = $data['TXS'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }
}
