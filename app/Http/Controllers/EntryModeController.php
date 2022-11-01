<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class EntryModeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $entryMode = DB::select("select accepted.ENTRY_MODE, accepted.ENTRY_MODE_DES FROM 
            (select main.ENTRY_MODE, entry.Entry_Mode_Des  from entrymode as entry inner join ".$request -> bd." as main on entry.entry_mode = main.ENTRY_MODE
            where main.CODIGO_RESPUESTA < '010' group by main.ENTRY_MODE, entry.Entry_Mode_Des) as accepted
            inner join
            (select main.ENTRY_MODE, entry.Entry_Mode_Des from entrymode as entry inner join ".$request -> bd." as main on entry.entry_mode = main.ENTRY_MODE
            where main.CODIGO_RESPUESTA >= '010'group by main.ENTRY_MODE, entry.Entry_Mode_Des) as rejected on accepted.ENTRY_MODE = rejected.ENTRY_MODE;");
        $array = json_decode(json_encode($entryMode), true); //Codificar arreglo asociativo

        $answer = array();

        foreach ($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID = $data['ENTRY_MODE'];
            $answer[$key]->Description = $data['ENTRY_MODE_DES'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }
    public function filterEntryMode(Request $request)
    {
        $values = array();
        $valuesExtra = array();
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

        $valuesExtra[0] = $request -> startDate;
        $valuesExtra[1] = $request -> finishDate;
        $valuesExtra[2] = $request -> startHour;
        $valuesExtra[3] = $request -> finishHour;

        $totalTX = 0;
        $response = array();
        $answer = array();
        $array = array();
        $arrayValues = array();

        $queryOutFilters = "select accepted.ENTRY_MODE, accepted.ENTRY_MODE_DES, accepted.MONTOA, accepted.TXSA, rejected.MONTOR, rejected.TXSR FROM 
        (select main.ENTRY_MODE, entry.Entry_Mode_Des,sum(main.MONTO1) AS MONTOA, count(*) as TXSA 
        from entrymode as entry inner join ".$request -> bd." as main on entry.entry_mode = main.ENTRY_MODE
        where main.CODIGO_RESPUESTA < '010' and (main.FECHA_TRANS >= ? and main.FECHA_TRANS <= ?) and (main.HORA_TRANS >= ? and main.HORA_TRANS <= ?)
        group by main.ENTRY_MODE, entry.Entry_Mode_Des) as accepted
        inner join
        (select main.ENTRY_MODE, entry.Entry_Mode_Des, sum(main.MONTO1) AS MONTOR, count(*) as TXSR 
        from entrymode as entry inner join ".$request -> bd." as main on entry.entry_mode = main.ENTRY_MODE
        where main.CODIGO_RESPUESTA >= '010'and (main.FECHA_TRANS >= ? and main.FECHA_TRANS <= ?) and (main.HORA_TRANS >= ? and main.HORA_TRANS <= ?)
        group by main.ENTRY_MODE, entry.Entry_Mode_Des) as rejected on accepted.ENTRY_MODE = rejected.ENTRY_MODE";

        $firstQuery = "select accepted.ENTRY_MODE, accepted.ENTRY_MODE_DES, accepted.MONTOA, accepted.TXSA, rejected.MONTOR, rejected.TXSR FROM 
        (select main.ENTRY_MODE, entry.Entry_Mode_Des,sum(main.MONTO1) AS MONTOA, count(*) as TXSA 
        from entrymode as entry inner join ".$request -> bd." as main on entry.entry_mode = main.ENTRY_MODE
        where main.CODIGO_RESPUESTA < '010' and (main.FECHA_TRANS >= ? and main.FECHA_TRANS <= ?) and (main.HORA_TRANS >= ? and main.HORA_TRANS <= ?) and ";
        $secondQuery = " group by main.ENTRY_MODE, entry.Entry_Mode_Des) as accepted
        inner join ( select main.ENTRY_MODE, entry.Entry_Mode_Des, sum(main.MONTO1) AS MONTOR, count(*) as TXSR 
        from entrymode as entry inner join ".$request -> bd." as main on entry.entry_mode = main.ENTRY_MODE
        where main.CODIGO_RESPUESTA >= '010' and (main.FECHA_TRANS >= ? and main.FECHA_TRANS <= ?) and (main.HORA_TRANS >= ? and main.HORA_TRANS <= ?) and ";
        $thirthQuery = " group by main.ENTRY_MODE, entry.Entry_Mode_Des) as rejected on accepted.ENTRY_MODE = rejected.ENTRY_MODE";

        //Eliminar los filtros que no han sido elegidos
        for($key = 0; $key < 11; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);

        if(empty($filteredValues)){
            $response = DB::select($queryOutFilters, [...$valuesExtra, ...$valuesExtra]);
            $array = json_decode(json_encode($response), true);
        }else{
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
                                $firstQuery .= "(" . $filteredLabels[$i] . " = ?)";
                                $secondQuery .= "(" . $filteredLabels[$i] . " = ?)";
                            } else {
                                $firstQuery .= "(" . $filteredLabels[$i] . " = ?) and ";
                                $secondQuery .= "(" . $filteredLabels[$i] . " = ?) and ";
                            }
                            $z++;
                        } else {
                            if ($z == count($arrayValues)) {
                                $firstQuery .= $filteredLabels[$i] . " = ?)";
                                $secondQuery .= $filteredLabels[$i] . " = ?)";
                                $z = 1;
                            } else {
                                $firstQuery .= $filteredLabels[$i] . " = ?) and ";
                                $secondQuery .= $filteredLabels[$i] . " = ?) and ";
                                $z++;
                            }
                        }
                    } else {
                        if ($j == 0) {
                            $firstQuery .= "(" . $filteredLabels[$i] . " = ? or ";
                            $secondQuery .= "(" . $filteredLabels[$i] . " = ? or ";
                            $z++;
                        } else {
                            $firstQuery .= $filteredLabels[$i] . " = ? or ";
                            $secondQuery .= $filteredLabels[$i] . " = ? or ";
                            $z++;
                        }
                    }
                }
            }
            //Consulta del query obtenido por los filtros y los valores elegidos
            //Se llena un nuevo arreglo con los valores obtenidos por el filtro pero duplicados,
            //esto para la ejecución del query (se requiere duplicado de los datos)
            $response = DB::select($firstQuery . $secondQuery . $thirthQuery, [...$valuesExtra, ...$arrayValues, ...$valuesExtra, ...$arrayValues]);
            $array = json_decode(json_encode($response), true);
        }

        foreach ($array as $keyTotal => $data) {
            $totalTX += $data['TXSA'] + $data['TXSR'];
        }

        foreach ($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID = $data['ENTRY_MODE'];
            $answer[$key]->Description = $data['ENTRY_MODE_DES'];
            //Separación del numero decimal y entero de ambos montos
            $decAccepted = substr($data['MONTOA'], strlen($data['MONTOA'])-2, 2);
            $intAccepted = substr($data['MONTOA'], 0, strlen($data['MONTOA'])-2);
            $answer[$key]->accepted_Amount = '$'.number_format($intAccepted.'.'.$decAccepted, 2);
            $decRejected = substr($data['MONTOR'], strlen($data['MONTOR'])-2, 2);
            $intRejected = substr($data['MONTOR'], 0, strlen($data['MONTOR'])-2);
            $answer[$key]->rejected_Amount = '$'.number_format($intRejected.'.'.$decRejected, 2);
            $answer[$key]->accepted_TX = number_format($data['TXSA']);
            $answer[$key]->rejected_TX = number_format($data['TXSR']);
            $answer[$key]->percenTX_Accepted = round((($data['TXSA'] / $totalTX) * 100), 2).'%';
            $answer[$key]->percenTX_Rejected = round((($data['TXSR'] / $totalTX) * 100), 2).'%';
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }
}
