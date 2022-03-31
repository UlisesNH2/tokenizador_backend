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
        $entryMode = DB::select("SELECT s.ENTRY_MODE,s.ENTRY_MODE_DES,s.MONTOA,s.TXSA,w.MONTOR,w.TXSR FROM (
        select t.ENTRY_MODE,e.Entry_Mode_Des,sum(t.MONTO1) AS MONTOA, count(*) as TXSA 
        from entrymode as e inner join test as t on e.entry_mode = t.ENTRY_MODE
        where t.CODIGO_RESPUESTA < '010'
        group by t.ENTRY_MODE,e.Entry_Mode_Des) as s
        inner join
        (
        select t.ENTRY_MODE,e.Entry_Mode_Des,sum(t.MONTO1) AS MONTOR, count(*) as TXSR 
        from entrymode as e inner join test as t on e.entry_mode = t.ENTRY_MODE
        where t.CODIGO_RESPUESTA >= '010'
        group by t.ENTRY_MODE,e.Entry_Mode_Des) as w on s.ENTRY_MODE = w.ENTRY_MODE");
        $array = json_decode(json_encode($entryMode), true); //Codificar arreglo asociativo

        $totalTX = 0;

        foreach($array as $keyTotal => $data){
            $totalTX += $data['TXSA'] + $data['TXSR'];
        }

        $answer = array();

        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> ID = $data['ENTRY_MODE'];
            $answer[$key] -> Description = $data['ENTRY_MODE_DES'];
            $answer[$key] -> accepted_Amount = number_format($data['MONTOA'], 2, '.');
            $answer[$key] -> accepted_TX = number_format($data['TXSA']);
            $answer[$key] -> rejected_Amount = number_format($data['MONTOR'], 2, '.');
            $answer[$key] -> rejected_TX = number_format($data['TXSR']);
            $answer[$key] -> percenTX_Accepted = round((($data['TXSA']/$totalTX) * 100), 2);
            $answer[$key] -> percenTX_Rejected = round((($data['TXSR']/$totalTX) * 100), 2);
        }

        $arrayJSON = json_decode(json_encode($answer), true);

        return $arrayJSON;
    }
    public function filterEntryMode(Request $request){

        $mode = $request -> entryMode;
        $totalTX = 0;
        $answer = array();

        $entryMode = DB::select("SELECT s.ENTRY_MODE,s.ENTRY_MODE_DES,s.MONTOA,s.TXSA,w.MONTOR,w.TXSR FROM (
        select t.ENTRY_MODE,e.Entry_Mode_Des,sum(t.MONTO1) AS MONTOA, count(*) as TXSA 
        from entrymode as e inner join test as t on e.entry_mode = t.ENTRY_MODE
        where t.CODIGO_RESPUESTA < '010'
        group by t.ENTRY_MODE,e.Entry_Mode_Des) as s
        inner join
        (
        select t.ENTRY_MODE,e.Entry_Mode_Des,sum(t.MONTO1) AS MONTOR, count(*) as TXSR 
        from entrymode as e inner join test as t on e.entry_mode = t.ENTRY_MODE
        where t.CODIGO_RESPUESTA >= '010'
        group by t.ENTRY_MODE,e.Entry_Mode_Des) as w on s.ENTRY_MODE = w.ENTRY_MODE");
        $array = json_decode(json_encode($entryMode), true); //Codificar arreglo asociativo
    
        foreach($array as $keyTotal => $data){
            $totalTX += $data['TXSA'] + $data['TXSR'];
        }
    
        foreach($array as $key => $data){
            if($data['ENTRY_MODE'] === $mode){
                $answer[$key] = new stdClass();
                $answer[$key] -> ID = $data['ENTRY_MODE'];
                $answer[$key] -> Description = $data['ENTRY_MODE_DES'];
                $answer[$key] -> accepted_Amount = number_format($data['MONTOA'], 2, '.');
                $answer[$key] -> accepted_TX = number_format($data['TXSA']);
                $answer[$key] -> rejected_Amount = number_format($data['MONTOR'], 2, '.');
                $answer[$key] -> rejected_TX = number_format($data['TXSR']);
                $answer[$key] -> percenTX_Accepted = round((($data['TXSA']/$totalTX) * 100), 2);
                $answer[$key] -> percenTX_Rejected = round((($data['TXSR']/$totalTX) * 100), 2);
            }
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        $arrayJSONOrdened = array_values($arrayJSON);
        return $arrayJSONOrdened;
    }
}
