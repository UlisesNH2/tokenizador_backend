<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    public function findUser(Request $request){

        $user = DB::select('select * from user where username = ?',[$request -> username]);
        if($user !== []){
            $arrayJson = json_decode(json_encode($user), true);
            //Verificar el password encriptado de la consulta con el request 
            if(password_verify($request -> password, $arrayJson[0]['password'])){
                $response = new stdClass();
                $response -> username = $arrayJson[0]['username'];
                $response -> name = $arrayJson[0]['name'];
                $response -> fisrtname = $arrayJson[0]['firstname'];
                $response -> secondname = $arrayJson[0]['secondname'];
                $response -> type = $arrayJson[0]['type'];
                $response -> logged = true;
                $responseUser = json_decode(json_encode($response), true);
                return $responseUser;
            }else{
                return -1;
            }
        }else { 
            return -2;
        }
    }
}
