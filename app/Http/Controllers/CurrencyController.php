<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class CurrencyController extends Controller
{
    public function getCurrCatalog(){
        $catalog = DB::select('select * from catalog_currency_code');
        $resp = json_decode(json_encode($catalog), true);
        $ans = array();

        foreach($resp as $key => $data){
            $ans[$key] = new stdClass();
            $ans[$key] -> code = $data['CURRENCY_CODE'];
        }
        $responseJSON = json_decode(json_encode($ans));
        return $responseJSON;
    }
}
