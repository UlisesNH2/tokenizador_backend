<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysqli;
use stdClass;

class ProjectController extends Controller
{
    public function uploadProject(Request $request){
        $projectExist = DB::select('select * from projects where ID = ?', [$request -> idProject]);
        if(empty($projectExist)){
            //Se registra el proyecto en la tabla 'projects' de la base de datos
            $createProject = DB::insert('insert into projects (ID, NOMBRE, NUMERO, EM_ADQ, BANCO, ID_USUARIO, FECHA_CREACION,
            FECHA_MODIFICACION, ORIGEN ) values (?,?,?,?,?,?,?,?,?)',[ 
            $request -> idProject, 
            $request -> name,
            $request -> number,
            $request -> origin,
            $request -> bank,
            $request -> userId,
            $request -> date,
            $request -> date,
            $request -> source]);
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
        $values = array();
        $arrayValues = array();
        $labels = ['ID', 'NOMBRE', 'NUMERO', 'EM_ADQ', 'BANCO', 'ORIGEN'];
        $values[0] = $request -> id;
        $values[1] = $request -> name;
        $values[2] = $request -> number;
        $values[3] = $request -> source;
        $values[4] = $request -> bank;
        $values[5] = $request -> tp;

        $queryOutFilters = 'select * from projects where ID_USUARIO = ?';
        $query = $queryOutFilters.' and ';

        //Eliminar los filtros que no han sido elegidos
        for($key = 0; $key < 6; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);

        if(empty($filteredValues)){ //En caso de que no se utilicen los filtros
            $response = DB::select($queryOutFilters, [$request -> userId]);
            $array = json_decode(json_encode($response), true);
        }else{
            //Ingresar todos los $filteredValues en un solo arreglo
            for ($i = 0; $i < count($filteredValues); $i++) {
                for ($j = 0; $j < count($filteredValues[$i]); $j++) {
                    array_push($arrayValues, $filteredValues[$i][$j]);
                }
            }
            $z = 1; //variable para el control de la longitud del query
            //Construcción del query varias consultas al mismo tiempo
            for ($i = 0; $i < count($filteredValues); $i++) {
                for ($j = 0; $j < count($filteredValues[$i]); $j++) {
                    if ($j == count($filteredValues[$i]) - 1) {
                        if ($j == 0) {
                            if ($z == count($arrayValues)) {
                                $query .= "(" . $filteredLabels[$i] . " = ?)";
                            } else {
                                $query .= "(" . $filteredLabels[$i] . " = ?) and ";
                            }
                            $z++;
                        } else {
                            if ($z == count($arrayValues)) {
                                $query .= $filteredLabels[$i] . " = ?)";
                                $z = 1;
                            } else {
                                $query .= $filteredLabels[$i] . " = ?) and ";
                                $z++;
                            }
                        }
                    } else {
                        if ($j == 0) {
                            $query .= "(" . $filteredLabels[$i] . " = ? or ";
                            $z++;
                        } else {
                            $query .= $filteredLabels[$i] . " = ? or ";
                            $z++;
                        }
                    }
                }
            }
            $response = DB::select($query, [$request -> userId, ...$arrayValues]);
            $array = json_decode(json_encode($response), true);
        }

        $ans = array();
        foreach($array as $key => $data){
            $ans[$key] = new stdClass();
            $ans[$key] -> id_proj = $data['ID'];
            $ans[$key] -> pro_name = $data['NOMBRE'];
            $ans[$key] -> number = $data['NUMERO'];
            $ans[$key] -> source = $data['EM_ADQ'];
            $ans[$key] -> bank = $data['BANCO'];
            $ans[$key] -> date = $data['FECHA_CREACION'];
            $ans[$key] -> date_up = $data['FECHA_MODIFICACION'];
            $ans[$key] -> tp = $data['ORIGEN'];
        }

        $proJSON = json_decode(json_encode($ans), true);
        return $proJSON;
    }

    public function updateProject(Request $request){
        $projectExist = DB::select('select ID from projects where ID = ?', [$request -> id]);
        if(!empty($projectExist)){
            $updateProject = DB::update('update projects set NOMBRE = ?, NUMERO = ?, EM_ADQ = ?, ORIGEN = ?, BANCO = ?, FECHA_MODIFICACION = ?  where ID = ?', [
                $request -> name,
                $request -> number,
                $request -> origin,
                $request -> source,
                $request -> bank,
                $request -> date,
                $request -> proj_id
            ]);
            if($updateProject == 1){
                $data = $request -> newData;
                //$host = "pyjcproas.duckdns.org";
                $host = "localhost";
                $user = "token_user";
                $pass = "";
                $bd = "prosa_test";
                $conn = new mysqli($host, $user, $pass, $bd, 3306);
                $queryDropTable = "drop table ".$request -> proj_id; //Eliminar la tabla
                if($conn -> query($queryDropTable)){
                    //Construcción del query para crear la tabla con la nueva data
                    $queryCreateTable = 'create table '.$request -> proj_id.' ( ';
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
                        $queryValues = "insert into ".$request -> proj_id." ".$columns . " values ";
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
        $existProject = DB::select('select ID from projects');
        if(!empty($existProject)){
            //Por jerarquía, se eliminará primero la tabla que contiene el proyecto dentro de la base de datos
            //$host = "pyjcproas.duckdns.org";
            $host = "localhost";
            $user = "token_user";
            $pass = "";
            $db = "prosa_test";
            $conn = new mysqli($host, $user, $pass, $db, 3306);
            if($conn -> query("drop table ".$request -> id)){
                $deleteProject = DB::delete('delete from projects where ID = ?', [$request -> id]);
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
        $answer = array();
        if($request -> tp === 'KM'){
            $datetime = DB::select('select max(FECHA_TRANS), min(FECHA_TRANS), max(HORA_TRANS), min(HORA_TRANS) from '.$request -> bd);
            $response = json_decode(json_encode($datetime), true);

            foreach($response as $key => $data){
                $answer[$key] = new stdClass();
                $answer[$key] -> startDate = $data['min(FECHA_TRANS)'];
                $answer[$key] -> finishDate = $data['max(FECHA_TRANS)'];
                $answer[$key] -> startHour = $data['min(HORA_TRANS)'];
                $answer[$key] -> finishHour = $data['max(HORA_TRANS)'];
            }
            $respJson = json_decode(json_encode($answer), true);
            return $respJson;
        }else{
            $datetime = DB::select("select max(FECHA), min(FECHA), max(HORA), min(HORA) from ".$request -> bd);
            $response = json_decode(json_encode($datetime), true);

            foreach($response as $key => $data){
                $answer[$key] = new stdClass();
                $answer[$key] -> startDate = $data['min(FECHA)'];
                $answer[$key] -> finishDate = $data['max(FECHA)'];
                $answer[$key] -> startHour = $data['min(HORA)'];
                $answer[$key] -> finishHour = $data['max(HORA)'];
            }
            $respJson = json_decode(json_encode($answer), true);
            return $respJson;
        }
    }
}
