<?php

namespace App\Http\Controllers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use mysqli;
use stdClass;

class ProjectController extends Controller
{
    public function uploadProject(Request $request){
        $projectExist = DB::select('select * from projects where ID_PROJECT = ?', [$request -> idProject]);
        if(empty($projectExist)){
            //Se registra el proyecto en la tabla 'projects' de la base de datos
            $createProject = DB::insert('insert into projects (ID_PROJECT, NOMBRE_PROYECTO, FECHA_CREACION, FECHA_MODIFICACION, ID_USUARIO )
            values (?,?,?,?,?)',[ 
            $request -> idProject, 
            $request -> name,
            $request -> date,
            $request -> date,
            $request -> userId]);
            if($createProject == 1){
                $data = $request -> data;
                $host = "pyjcproas.duckdns.org";
                //$host = "localhost";
                $user = "token_user";
                $pass = "";
                $bd = "prosa_test";
                $flag = false;
                $conn = new mysqli($host, $user, $pass, $bd, 3306);
                //Subir el proyecto a la base de datosX
                //Construcción del query para crear la base de datos
                $queryCreateTable = 'create table '.$request -> idProject.' ( ';
                $columns = '(';
                for($i = 0; $i < count($data[0]); $i++){
                    if($i === count($data[0])-1){
                        $columns .= $data[0][$i].')';
                        $queryCreateTable .= $data[0][$i]." text NULL )";
                    }else{
                        $columns .= $data[0][$i].', ';
                        $queryCreateTable .= $data[0][$i]." text NULL, ";
                    }
                }
                if($conn -> query($queryCreateTable)){ mysqli_close($conn); $flag = true; } 
                else { mysqli_close($conn); }
                if($flag){
                    $queryValues = "insert into ".$request->idProject." ".$columns . " values ";
                    for($i = 1; $i < count($data); $i++){
                        $queryValues .= '(';
                        for ($j = 0; $j < count($data[$i]); $j++) {
                            if($j === count($data[$i]) - 1 && $i === count($data) -1){
                                $queryValues .= "'".$data[$i][$j]."'".")";
                            } else if($j === count($data[$i]) - 1 ) {
                                $queryValues .= "'".$data[$i][$j]."'),";
                            }else{
                                $queryValues .= "'".$data[$i][$j]."', ";
                            }
                        }
                    }
                }
                $insertValues = DB::insert($queryValues);
                return $insertValues;
            }
        }else{ return -1; }
    }

    public function getProjects(Request $request){
        $projects = DB::select('select ID_PROJECT, NOMBRE_PROYECTO, FECHA_CREACION, FECHA_MODIFICACION, ID_USUARIO from projects where ID_USUARIO = ?', [
            $request -> userId
        ]);
        $proArr = json_decode(json_encode($projects), true);

        $ans = array();
        foreach($proArr as $key => $data){
            $ans[$key] = new stdClass();
            $ans[$key] -> id_proj = $data['ID_PROJECT'];
            $ans[$key] -> pro_name = $data['NOMBRE_PROYECTO'];
            $ans[$key] -> date = $data['FECHA_CREACION'];
            $ans[$key] -> date_up = $data['FECHA_MODIFICACION'];
        }

        $proJSON = json_decode(json_encode($ans), true);
        return $proJSON;
    }

    public function updateProject(Request $request){
        $projectExist = DB::select('select ID_PROJECT from projects where ID_PROJECT = ?', [$request -> id]);
        if(!empty($projectExist)){
            $updateProject = DB::update('update projects set ID_PROJECT = ?, NOMBRE_PROYECTO = ?, FECHA_MODIFICACION = ? where ID_PROJECT = ?', [
                $request -> newIdProj,
                $request -> name_proj,
                $request -> upDate,
                $request -> id
            ]);
            if($updateProject == 1){
                $data = $request -> newData;
                $host = "pyjcproas.duckdns.org";
                //$host = "localhost";
                $user = "token_user";
                $pass = "";
                $bd = "prosa_test";
                $conn = new mysqli($host, $user, $pass, $bd, 3306);
                $queryDropTable = "drop table ".$request -> id; //Eliminar la tabla
                if($conn -> query($queryDropTable)){
                    //Construcción del query para crear la tabla con la nueva data
                    $queryCreateTable = 'create table '.$request -> newIdProj.' ( ';
                    $columns = '(';
                    for($i = 0; $i < count($data[0]); $i++){
                        if($i === count($data[0])-1){
                            $columns .= $data[0][$i].')';
                            $queryCreateTable .= $data[0][$i]." text NULL )";
                        }else{
                            $columns .= $data[0][$i].', ';
                            $queryCreateTable .= $data[0][$i]." text NULL, ";
                        }
                    }
                    if($conn -> query($queryCreateTable)){
                        //Insertar los valores a la nueva tabla
                        $queryValues = "insert into ".$request -> newIdProj." ".$columns . " values ";
                        for ($i = 1; $i < count($data); $i++) {
                            $queryValues .= '(';
                            for ($j = 0; $j < count($data[$i]); $j++) {
                                if ($j === count($data[$i]) - 1 && $i === count($data) - 1) {
                                    $queryValues .= "'" . $data[$i][$j] . "'" . ")";
                                } else if ($j === count($data[$i]) - 1) {
                                    $queryValues .= "'" . $data[$i][$j] . "'),";
                                } else {
                                    $queryValues .= "'" . $data[$i][$j] . "', ";
                                }
                            }
                        }
                        mysqli_close($conn);
                        $insertValues = DB::insert($queryValues);
                        return $insertValues;
                    }else{
                        return -1;
                    }
                }else{
                    return -2;
                }
            }else{
                return -3;
            }
        }else{
            return -4;
        }
    }

    public function deleteProject(Request $request){
        $existProject = DB::select('select ID_PROJECT from projects');
        if(!empty($existProject)){
            //Por jerarquía, se eliminará primero la tabla que contiene el proyecto dentro de la base de datos
            $host = "pyjcproas.duckdns.org";
            //host = "localhost";
            $user = "token_user";
            $pass = "";
            $db = "prosa_test";
            $conn = new mysqli($host, $user, $pass, $db, 3306);
            if($conn -> query("drop table ".$request -> id)){
                $deleteProject = DB::delete('delete from projects where ID_PROJECT = ?', [$request -> id]);
                mysqli_close($conn);
                return $deleteProject;
            }else{
                return -1;
            }
        }else{
            return -2;
        }
    }

    public function getDateAndTime(Request $request){
        $datetime = DB::select('select max(FECHA_TRANS), min(FECHA_TRANS), max(HORA_TRANS), min(HORA_TRANS) from '.$request -> bd);
        $response = json_decode(json_encode($datetime), true);

        $answer = array();
        foreach($response as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> startDate = $data['min(FECHA_TRANS)'];
            $answer[$key] -> finishDate = $data['max(FECHA_TRANS)'];
            $answer[$key] -> startHour = $data['min(HORA_TRANS)'];
            $answer[$key] -> finishHour = $data['max(HORA_TRANS)'];
        }
        $respJson = json_decode(json_encode($answer), true);
        return $respJson;
    }
}
