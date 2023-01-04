<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class codeResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request -> tp === 'KM'){
            $codeResponse = DB::select("select main.CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES 
            from ".$request -> bd." as main inner join codrespuesta as code on main.CODIGO_RESPUESTA = code.CODIGO_RESPUESTA 
            group by CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES");
            $array = json_decode(json_encode($codeResponse), true); //Codificar un array asociativo
            $answer = array();
            foreach ($array as $key => $data) {
                $answer[$key] = new stdClass();
                $answer[$key]->ID = $data['CODIGO_RESPUESTA'];
                $answer[$key]->Description = $data['CODIGO_RESPUESTA_DES'];
            }
        }else{
            $codeResponse = DB::select("select main.RESPUESTA, code.CODIGO_RESPUESTA_DES 
            from ".$request -> bd." as main inner join codrespuesta as code on main.RESPUESTA = code.CODIGO_RESPUESTA 
            group by main.RESPUESTA, code.CODIGO_RESPUESTA_DES");
            $array = json_decode(json_encode($codeResponse), true); //Codificar un array asociativo
            $answer = array();
            foreach ($array as $key => $data) {
                $answer[$key] = new stdClass();
                $answer[$key]->ID = $data['RESPUESTA'];
                $answer[$key]->Description = $data['CODIGO_RESPUESTA_DES'];
            }
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }

    public function filterCodeResponse(Request $request)
    {
        $values = array();
        $valuesExtra = array();
        $labels = [];

        $values[0] = $request -> kq2;
        $values[1] = $request -> codeResponse;
        $values[2] = $request -> entryMode;
        $values[3] = $request -> ID_Comer;
        $values[4] = $request -> Term_Comer;
        $values[5] = $request -> Fiid_Comer;
        $values[6] = $request -> Fiid_Term;
        $values[7] = $request -> Ln_Comer;
        $values[8] = $request -> Ln_Term;
        $values[9] = $request -> Fiid_Card;
        $values[10] = $request -> Ln_Card; 
        $valuesExtra[0] = $request -> startDate;
        $valuesExtra[1] = $request -> finishDate;
        $valuesExtra[2] = $request -> startHour;
        $valuesExtra[3] = $request -> finishHour;

        $array = array();
        $response = array();
        $answer = array();
        $response = array();
        $arrayValues = array();

        switch($request -> tp){
            case 'KM': {
                $labels = ['main.KQ2_ID_MEDIO_ACCESO', 'main.CODIGO_RESPUESTA', 'main.ENTRY_MODE', 'main.ID_COMER', 'main.TERM_COMER', 
                'main.FIID_COMER', 'main.FIID_TERM', 'main.LN_COMER', 'main.LN_TERM', 'main.FIID_TARJ', 'main.LN_TARJ'];

                $queryOutFilters = "select main.CODIGO_RESPUESTA, codeResp.CODIGO_RESPUESTA_DES, MONTO1 from ".$request -> bd." as main inner join
                codrespuesta as codeResp on codeResp.CODIGO_RESPUESTA = main.CODIGO_RESPUESTA 
                where (main.FECHA_TRANS >= ? and main.FECHA_TRANS <= ?)
                and (main.HORA_TRANS >= ? and main.HORA_TRANS <= ?)";

                $query = $queryOutFilters. ' and ';
                break;
            }
            case 'PTLF' : {
                $labels = ['main.TKN_Q2_ID_ACCESO', 'main.RESPUESTA', 'main.PEM', 'main.TERM_ID', 'main.TERM_ID', 
                'main.ADQUIRENTE', 'main.ADQUIRENTE','main.RED', 'main.RED', 'main.EMISOR', 'main.LN'];
                
                $queryOutFilters = "select main.RESPUESTA, cde.CODIGO_RESPUESTA_DES, MONTO from ".$request ->bd." as 
                main inner join codrespuesta as cde on cde.CODIGO_RESPUESTA = main.RESPUESTA 
                where (main.FECHA >= ? and main.FECHA <= ?) 
                and (main.HORA >= ? and main.HORA <= ?)";

                $query = $queryOutFilters. ' and ';
                break;
            }
        }      
        $totalTX = 0;
        //Eliminar filtros no seleccionados
        for($key = 0; $key < 11; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);

        if(empty($filteredValues)){ //En caso de que no se utilicen los filtros
            $response = DB::select($queryOutFilters, [...$valuesExtra]);
            $array = json_decode(json_encode($response), true);
        }else{
            //Ingresar todos los $filteredValues en un solo arreglo
            for ($i = 0; $i < count($filteredValues); $i++) {
                for ($j = 0; $j < count($filteredValues[$i]); $j++) {
                    array_push($arrayValues, $filteredValues[$i][$j]);
                }
            }
            $z = 1; //variable para el control de la longitud del query
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
            $response = DB::select($query, [...$valuesExtra, ...$arrayValues]);
            $array = json_decode(json_encode($response), true);
        }

        switch($request -> tp){
            case 'KM': {
                foreach ($array as $key => $data) {
                    $answer[$key] = new stdClass();
                    $answer[$key] -> ID = $data['CODIGO_RESPUESTA'];
                    $answer[$key] -> Description = $data['CODIGO_RESPUESTA_DES'];
                    //Separación de cifra decimal y entera para el monto
                    $dec = substr($data['MONTO1'], strlen($data['MONTO1'])-2, 2);
                    $int = substr($data['MONTO1'], 0, strlen($data['MONTO1'])-2);
                    $answer[$key] -> amount = $int.".".$dec;
                }
                break;
            }
            case 'PTLF': {
                foreach($array as $key => $data){
                    $answer[$key] = new stdClass();
                    $answer[$key] -> ID = $data['RESPUESTA'];
                    $answer[$key] -> Description = $data['CODIGO_RESPUESTA_DES'];
                    $answer[$key] -> amount = $data['MONTO'];
                }
                break;
            }
        }
        
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }

    public function getCodeResp(){
        $catalog = DB::select('select * from codrespuesta');
        $datajson = json_decode(json_encode($catalog), true);

        foreach($datajson as $key => $data){
            $response[$key] = new stdClass();
            $response[$key] -> cdeResp = $data['CODIGO_RESPUESTA'];
            $response[$key] -> cdeRespDes = $data['CODIGO_RESPUESTA_DES'];
        }

        return json_decode(json_encode($response), true);
    }
}
