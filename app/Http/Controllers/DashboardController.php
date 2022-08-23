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
        $values = array();
        $labels = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'ID_COMER', 'TERM_COMER', 'FIID_COMER', 'FIID_TERM',
        'LN_COMER', 'LN_TERM', 'FIID_TARJ', 'LN_TARJ', 'FECHA_TRANS'];
        
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
        //$values[11] = $request -> startDate;
        //$values[12] = $request -> finishDate;

        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM,
        LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, sum(MONTO1) AS MONTO, count(*) as TXS from test 
        group by KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM,
        LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ having ";

        $queryOutFilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM,
        LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, sum(MONTO1) AS MONTO, count(*) as TXS from test group by KQ2_ID_MEDIO_ACCESO, 
        CODIGO_RESPUESTA, ENTRY_MODE, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM,
        LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ";
        $response = array();
        $array = array();
        $arrayValues = array();

        //Eliminar los values y los arrays que no se estén utilizando
        for($key = 0; $key < 11; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);

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
                //Ingresar todos los valores elegidos en el filtro dentro de un solo arreglo. (Valores para la consulta)
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        array_push($arrayValues, $filteredValues[$i][$j]);
                    }
                }
                $z = 1; //Variable 'controladora' de el largo del query
                //Constructor del query (Varias consultas al mismo tiempo)
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
                //Consulta del query obtenido por los filtros y los valores elegidos
                return $query."and (FECHA_TRANS between '210314' and '210330')";
                $response = DB::select($query, [...$arrayValues]);
                $array = json_decode(json_encode($response), true);
            }
        }
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
            $answer[$key] -> Fiid_Comer = $data['FIID_COMER'];
            $answer[$key] -> Fiid_Term = $data['FIID_TERM'];
            $answer[$key] -> Fiid_Tarj = $data['FIID_TARJ'];
            $answer[$key] -> Ln_Comer = $data['LN_COMER'];
            $answer[$key] -> Ln_Term = $data['LN_TERM'];
            $answer[$key] -> Ln_Tarj = $data['LN_TARJ'];
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
