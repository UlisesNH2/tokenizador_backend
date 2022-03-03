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
    public function index()
    {
        $kq2 = DB::select("SELECT s.KQ2_ID_MEDIO_ACCESO,s.KQ2_ID_MEDIO_ACCESO_DES,s.MONTOA,s.TXSA,w.MONTOR,w.TXSR FROM (
        select t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES,sum(t.MONTO1) AS MONTOA, count(*) as TXSA 
        from medioacceso as e inner join test as t on e.KQ2_ID_MEDIO_ACCESO = t.KQ2_ID_MEDIO_ACCESO
        where t.CODIGO_RESPUESTA < '010'
        group by t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES) as s
        inner join
        (
        select t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES,sum(t.MONTO1) AS MONTOR, count(*) as TXSR 
        from medioacceso as e inner join test as t on e.KQ2_ID_MEDIO_ACCESO = t.KQ2_ID_MEDIO_ACCESO
        where t.CODIGO_RESPUESTA >= '010'
        group by t.KQ2_ID_MEDIO_ACCESO,e.KQ2_ID_MEDIO_ACCESO_DES) as w on s.KQ2_ID_MEDIO_ACCESO = w.KQ2_ID_MEDIO_ACCESO");
        $array = json_decode(json_encode($kq2), true); //Codificar un array asociativo

        $TX_Desciptions = [];
        $TX_Mode = [];
        $TX_Accepted = [];
        $TX_Rejected = [];
        $percenTX_Accepted = [];
        $percenTX_Rejected = [];

        $totalTX = 0;

        foreach($array as $key => $data){
            array_push($TX_Desciptions, $data['KQ2_ID_MEDIO_ACCESO_DES']);
            array_push($TX_Mode, $data['KQ2_ID_MEDIO_ACCESO']);
            array_push($TX_Accepted, $data['TXSA']);
            array_push($TX_Rejected, $data['TXSR']);
            $totalTX += $data['TXSA'] + $data['TXSR'];
        }

        foreach($array as $key => $dataPercent){
            array_push($percenTX_Accepted, round((($dataPercent['TXSA'] / $totalTX) * 100), 2));
            array_push($percenTX_Rejected, round((($dataPercent['TXSR'] / $totalTX) * 100), 2));
        }

        $answer = new stdClass();
        $answer -> TX_Descriptions = $TX_Desciptions;
        $answer -> TX_Accepted = $TX_Accepted;
        $answer -> TX_Rejected = $TX_Rejected;
        $answer -> percenTX_Accepted = $percenTX_Accepted;
        $answer -> percenTX_Rejected = $percenTX_Rejected;

        return $answer;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
