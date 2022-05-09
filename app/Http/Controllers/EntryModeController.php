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
    public function index()
    {
        $entryMode = DB::select("select accepted.ENTRY_MODE, accepted.ENTRY_MODE_DES FROM 
            (select main.ENTRY_MODE, entry.Entry_Mode_Des  from entrymode as entry inner join test as main on entry.entry_mode = main.ENTRY_MODE
            where main.CODIGO_RESPUESTA < '010' group by main.ENTRY_MODE, entry.Entry_Mode_Des) as accepted
            inner join
            (select main.ENTRY_MODE, entry.Entry_Mode_Des from entrymode as entry inner join test as main on entry.entry_mode = main.ENTRY_MODE
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

        $entryMode = $request->entryMode;
        $totalTX = 0;
        $response = array();
        $answer = array();

        $query = "select accepted.ENTRY_MODE, accepted.ENTRY_MODE_DES, accepted.MONTOA, accepted.TXSA, rejected.MONTOR, rejected.TXSR FROM 
        (select main.ENTRY_MODE, entry.Entry_Mode_Des,sum(main.MONTO1) AS MONTOA, count(*) as TXSA 
        from entrymode as entry inner join test as main on entry.entry_mode = main.ENTRY_MODE
        where main.CODIGO_RESPUESTA < '010'
        group by main.ENTRY_MODE, entry.Entry_Mode_Des) as accepted
        inner join
        (select main.ENTRY_MODE, entry.Entry_Mode_Des, sum(main.MONTO1) AS MONTOR, count(*) as TXSR 
        from entrymode as entry inner join test as main on entry.entry_mode = main.ENTRY_MODE
        where main.CODIGO_RESPUESTA >= '010'
        group by main.ENTRY_MODE, entry.Entry_Mode_Des) as rejected on accepted.ENTRY_MODE = rejected.ENTRY_MODE";

        $queryFilter = "select accepted.ENTRY_MODE, accepted.ENTRY_MODE_DES, accepted.MONTOA, accepted.TXSA, rejected.MONTOR, rejected.TXSR FROM 
        (select main.ENTRY_MODE, entry.Entry_Mode_Des,sum(main.MONTO1) AS MONTOA, count(*) as TXSA 
        from entrymode as entry inner join test as main on entry.entry_mode = main.ENTRY_MODE
        where main.CODIGO_RESPUESTA < '010' and main.ENTRY_MODE = ?
        group by main.ENTRY_MODE, entry.Entry_Mode_Des) as accepted
        inner join
        ( select main.ENTRY_MODE, entry.Entry_Mode_Des, sum(main.MONTO1) AS MONTOR, count(*) as TXSR 
        from entrymode as entry inner join test as main on entry.entry_mode = main.ENTRY_MODE
        where main.CODIGO_RESPUESTA >= '010' and main.ENTRY_MODE = ?
        group by main.ENTRY_MODE, entry.Entry_Mode_Des) as rejected on accepted.ENTRY_MODE = rejected.ENTRY_MODE";

        if (!empty($entryMode)) {
            for ($i = 0; $i < count($entryMode); $i++) {
                $response = array_merge($response, DB::select($queryFilter, [$entryMode[$i], $entryMode[$i]]));
            }
            $array = json_decode(json_encode($response), true);
        } else {
            $response = array_merge($response, DB::select($query));
            $array = json_decode(json_encode($response), true);
        }

        foreach ($array as $keyTotal => $data) {
            $totalTX += $data['TXSA'] + $data['TXSR'];
        }

        foreach ($array as $key => $data) {
            $answer[$key] = new stdClass();
            $answer[$key]->ID = $data['ENTRY_MODE'];
            $answer[$key]->Description = $data['ENTRY_MODE_DES'];
            $answer[$key]->accepted_Amount = number_format($data['MONTOA'], 2, '.');
            $answer[$key]->accepted_TX = number_format($data['TXSA']);
            $answer[$key]->rejected_Amount = number_format($data['MONTOR'], 2, '.');
            $answer[$key]->rejected_TX = number_format($data['TXSR']);
            $answer[$key]->percenTX_Accepted = round((($data['TXSA'] / $totalTX) * 100), 2);
            $answer[$key]->percenTX_Rejected = round((($data['TXSR'] / $totalTX) * 100), 2);
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }
}
