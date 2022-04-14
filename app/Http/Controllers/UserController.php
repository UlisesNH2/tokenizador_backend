<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = DB::select('select id, name, firstname, secondname, username, type from user where id != ?', [$request -> id]);
        $arrayJson = json_decode(json_encode($user), true);
        return $arrayJson;
    }

    public function findUser(Request $request)
    {
        $user = DB::select('select * from user where username = ?',[$request -> username]);
        if($user !== []){
            $arrayJson = json_decode(json_encode($user), true);
            //Verificar el password encriptado de la consulta con el request 
            if(password_verify($request -> password, $arrayJson[0]['password'])){
                $response = new stdClass();
                $response -> id = $arrayJson[0]['id'];
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

    public function createUser(Request $request)
    {
        $userExist = DB::select('select username from user where username = ?',[$request -> username]);
        if($userExist == []){
            $password = password_hash($request -> password, PASSWORD_BCRYPT); //Encriptación del password
            $user = DB::insert('insert into user (name, firstname, secondname, username, password, type)
            values (?,?,?,?,?,?)',[
            $request -> name,
            $request -> firstname,
            $request -> secondname,
            $request -> username,
            $password,
            $request -> type
            ]);
            return $user;
        }else {return -1;}
    }
}
