<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class Kq2Controller extends Controller
{
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request -> tp === 'KM'){

            $kq2 = DB::select("select main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES from medioacceso as kq2
            inner join ".$request -> bd." as main on kq2.KQ2_ID_MEDIO_ACCESO = main.KQ2_ID_MEDIO_ACCESO");
            
            $array = json_decode(json_encode($kq2), true); //Codificar un array asociativo
            $answer = array();
            foreach($array as $key => $data){
                $answer[$key] = new stdClass();
                $answer[$key] -> ID = $data['KQ2_ID_MEDIO_ACCESO'];
                $answer[$key] -> Description = $data['KQ2_ID_MEDIO_ACCESO_DES'];
            }
        }else{

            $kq2 = DB::select("select main.TKN_Q2_ID_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES from medioacceso as kq2
            inner join ".$request -> bd." as main on kq2.KQ2_ID_MEDIO_ACCESO = main.TKN_Q2_ID_ACCESO");

            $array = json_decode(json_encode($kq2), true); //Codificar un array asociativo
            $answer = array();
            foreach($array as $key => $data){
                $answer[$key] = new stdClass();
                $answer[$key] -> ID = $data['TKN_Q2_ID_ACCESO'];
                $answer[$key] -> Description = $data['KQ2_ID_MEDIO_ACCESO_DES'];
            }
        }
        
        $arrayJson = json_decode(json_encode(array_values(array_unique($answer, SORT_REGULAR))), true);
        return $arrayJson;
    }

    public function filterKq2(Request $request){

        $values = array();
        $valuesExtra = array();
        
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
        $arrayValues = array();
        $answer = array();
        $labels = [];

        //Validación del tipo de proyecto que se está manejando en frontend
        switch($request -> tp){
            case 'KM' : {
                $labels = ['main.KQ2_ID_MEDIO_ACCESO', 'main.CODIGO_RESPUESTA', 'main.ENTRY_MODE', 'main.ID_COMER', 'main.TERM_COMER', 
                'main.FIID_COMER', 'main.FIID_TERM','main.LN_COMER', 'main.LN_TERM', 'main.FIID_TARJ', 'main.LN_TARJ'];
                
                $queryOutFilters = "select main.KQ2_ID_MEDIO_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES, MONTO1, CODIGO_RESPUESTA from ".$request ->bd." as 
                main inner join medioacceso as kq2 on kq2.KQ2_ID_MEDIO_ACCESO = main.KQ2_ID_MEDIO_ACCESO 
                where (main.FECHA_TRANS >= ? and main.FECHA_TRANS <= ?) 
                and (main.HORA_TRANS >= ? and main.HORA_TRANS <= ?)";

                $query = $queryOutFilters. 'and ';
                break;
            }
            case 'PTLF': {
                $labels = ['main.TKN_Q2_ID_ACCESO', 'main.RESPUESTA', 'main.PEM', 'main.TERM_ID', 'main.TERM_ID', 
                'main.ADQUIRENTE', 'main.ADQUIRENTE','main.RED', 'main.RED', 'main.EMISOR', 'main.LN'];
                
                $queryOutFilters = "select main.TKN_Q2_ID_ACCESO, kq2.KQ2_ID_MEDIO_ACCESO_DES, MONTO, RESPUESTA from ".$request ->bd." as 
                main inner join medioacceso as kq2 on kq2.KQ2_ID_MEDIO_ACCESO = main.TKN_Q2_ID_ACCESO 
                where (main.FECHA >= ? and main.FECHA <= ?) 
                and (main.HORA >= ? and main.HORA <= ?)";

                $query = $queryOutFilters. 'and ';
                break;
            }
        }

        //Eliminar los filtros que no han sido elegidos
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
                foreach($array as $key => $data){
                    $answer[$key] = new stdClass();
                    $answer[$key] -> ID = $data['KQ2_ID_MEDIO_ACCESO'];
                    $answer[$key] -> Description = $data['KQ2_ID_MEDIO_ACCESO_DES'];
                    $answer[$key] -> code_Resp = $data['CODIGO_RESPUESTA'];
                    $dec = substr($data['MONTO1'], strlen($data['MONTO1'])-2, 2);
                    $int = substr($data['MONTO1'], 0, strlen($data['MONTO1'])-2);
                    $answer[$key] -> amount = $int.".".$dec;
                }
                break;
            }
            case 'PTLF': {
                foreach($array as $key => $data){
                    $answer[$key] = new stdClass();
                    $answer[$key] -> ID = $data['TKN_Q2_ID_ACCESO'];
                    $answer[$key] -> Description = $data['KQ2_ID_MEDIO_ACCESO_DES'];
                    $answer[$key] -> code_Resp = $data['RESPUESTA'];
                    $answer[$key] -> amount = $data['MONTO'];
                }
                break;
            }
        }
        
        $arrayJson = json_decode(json_encode($answer), true);
        return $arrayJson;
    }

    public function getKq2(){
        $catalog = DB::select('select * from medioacceso order by KQ2_ID_MEDIO_ACCESO');
        $dataJson = json_decode(json_encode($catalog), true);
        $response = array();

        foreach($dataJson as $key => $data){
            $response[$key] = new stdClass();
            $response[$key] -> q2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $response[$key] -> q2Desc = $data['KQ2_ID_MEDIO_ACCESO_DES'];
        }

        return json_decode(json_encode($response), true);
    }
}
