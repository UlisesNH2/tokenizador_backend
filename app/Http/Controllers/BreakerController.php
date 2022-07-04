<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BreakerController extends Controller
{
    public function getBreakes(Request $request){
        return $request -> message;
    }
}
