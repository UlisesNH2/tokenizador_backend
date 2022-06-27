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
    public function index()
    {
        $codeResponse = DB::select("select main.CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES 
        from test as main inner join codrespuesta as code on main.CODIGO_RESPUESTA = code.CODIGO_RESPUESTA 
        group by CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES");
        $array = json_decode(json_encode($codeResponse), true); //Codificar un array asociativo
        $answer = array();
        foreach ($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID = $data['CODIGO_RESPUESTA'];
            $answer[$key]->Description = $data['CODIGO_RESPUESTA_DES'];
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;
    }

    public function filterCodeResponse(Request $request)
    {
        $values = array();
        $labels = ['main.KQ2_ID_MEDIO_ACCESO', 'main.CODIGO_RESPUESTA', 'main.ENTRY_MODE', 'main.ID_COMER', 'main.TERM_COMER', 
        'main.FIID_COMER', 'main.FIID_TERM', 'main.LN_COMER', 'main.LN_TERM', 'main.FIID_TARJ', 'main.LN_TARJ'];

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

        $array = array();
        $response = array();
        $answer = array();
        $response = array();
        $arrayValues = array();
        $queryOutFilters = "select main.CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES, sum(main.MONTO1) AS MONTO, count(*) as TXS 
        from test as main inner join codrespuesta as code on main.CODIGO_RESPUESTA = code.CODIGO_RESPUESTA 
        group by CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES";
        $firstQuery = "select main.CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES, sum(main.MONTO1) AS MONTO, count(*) as TXS 
        from test as main inner join codrespuesta as code on main.CODIGO_RESPUESTA = code.CODIGO_RESPUESTA
        where ";
        $secondQuery = " group by CODIGO_RESPUESTA, code.CODIGO_RESPUESTA_DES";
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

        if(empty($filteredValues)){
            $response = DB::select($queryOutFilters);
            $array = json_decode(json_encode($response), true);
        }else{
            if(count($filteredValues) <= 1){
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        $response = array_merge($response, DB::select($firstQuery.$filteredLabels[$i]." = ?".$secondQuery,
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
                                    $firstQuery .= "(".$filteredLabels[$i]." = ?)";
                                }else{
                                    $firstQuery .= "(".$filteredLabels[$i]." = ?) and ";
                                }
                                $z++;
                            }else{
                                if($z == count($arrayValues)){
                                    $firstQuery .= $filteredLabels[$i]." = ?)";
                                    $z = 1;
                                }else{
                                    $firstQuery .= $filteredLabels[$i]." = ?) and ";
                                    $z++;
                                }
                            }
                        }else{
                            if($j == 0){
                                $firstQuery .= "(".$filteredLabels[$i]." = ? or ";
                                $z++;
                            }else{
                                $firstQuery .= $filteredLabels[$i]." = ? or ";
                                $z++;
                            }
                        }
                    }
                }
                //Consulta del query obtenido por los filtros y los valores elegidos
                return $firstQuery.$secondQuery;
                $response = DB::select($firstQuery, [...$arrayValues]);
                $array = json_decode(json_encode($response), true);
            }
        }        
        foreach($array as $key => $data){
            $totalTX += $data['TXS'];
        }

        foreach ($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID = $data['CODIGO_RESPUESTA'];
            $answer[$key]->Description = $data['CODIGO_RESPUESTA_DES'];
            //Separación de cifra decimal y entera para el monto
            $dec = substr($data['MONTO'], strlen($data['MONTO'])-2, 2);
            $int = substr($data['MONTO'], 0, strlen($data['MONTO'])-2);
            $answer[$key]->CodeResp_Amount = '$'.number_format($int.".".$dec, 2);
            $answer[$key]->CodeResp_TXS = number_format($data['TXS']);
            $answer[$key]->CodeResp_Percent = round(($data['TXS'] / $totalTX * 100), 2).'%';
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }
}
