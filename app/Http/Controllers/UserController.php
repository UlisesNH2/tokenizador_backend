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
        if(!empty($user)){
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
        if(empty($userExist)){
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

    public function updateUser(Request $request){
        $userExist = DB::select('select username from user where id = ?', [$request -> id]);
        if(!empty($userExist)){
            $user = DB::update('update user set name = ?, firstname = ?, secondname = ?,
            username = ?, type = ? where id = ?', [
                $request -> name,
                $request -> firstName,
                $request -> secondName,
                $request -> userName,
                $request -> type,
                $request -> id
            ]);
            return $user;
        }else { return -1; } 
    }

    public function deleteUser(Request $request){
        $userExist = DB::select('select username from user where id = ?', [$request -> id]);
        if(!empty($userExist)){
            $user = DB::delete('delete from user where id = ?', [$request -> id]);
            return $user;
        }else { return -1; }
    }

    public function updatePersonalData(Request $request){
        $userExist = DB::select('select username from user where id = ?', [$request -> id]);
        if(!empty($userExist)){
            $user = DB::update('update user set name = ?, firstname = ?, secondname = ?, username = ? where id = ?',[
                $request -> name,
                $request -> firstname,
                $request -> secondname,
                $request -> username,
                $request -> id
            ]);
            return $user;
        }else { return -1; }
    }

    public function updatePassword(Request $request){
        $userExist = DB::select('select password from user where id = ?', [$request -> id]);
        $response = json_decode(json_encode($userExist), true);
        if(!empty($userExist)){
            if(!password_verify($request -> password, $response[0]['password'])){
                $password = password_hash($request -> password, PASSWORD_BCRYPT); //Encriptación del password
                $user = DB::update('update user set password = ? where id = ?', [
                    $password,
                    $request -> id
                ]);
            }else{
                return -2;
            }
            return $user;
        }else{ return -1; }
    }
}
