<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Constraints\CountInDatabase;

class TerminalController extends Controller
{
    public function index(Request $request){
        $kq2 = $request->Kq2;
        $codeResponse = $request->Code_Response;
        $entryMode = $request->Entry_Mode;
        $numberFilters = 0;
        $flagkq2 = false;
        $flagCode = false;
        $flagEntry = false;
        $queryID_COMER = "select ID_COMER from test";

        $queryTERM_COMER = "select TERM_COMER from test";

        $queryFIID_COMER = "select main.FIID_COMER, catComer.FIID_COMER_DES from test as main
        join fiid_comer as catComer on main.FIID_COMER = catComer.FIID_COMER";

        $queryFIID_TARJ = "select main.FIID_TARJ, catTarj.FIID_TARJ_DES from test as main 
        join fiid_tarj as catTarj on main.FIID_TARJ = catTarj.FIID_TARJ";

        $queryFIID_TERM = "select main.FIID_TERM, catComer.FIID_COMER_DES from test as main 
        join fiid_comer as catComer on main.FIID_COMER = catComer.FIID_COMER";

        $queryLN_COMER = "select main.LN_COMER, catLNComer.LN_COMER_DES from test as main 
        join ln_comer as catLNComer on main.LN_COMER = catLNComer.LN_COMER";

        $queryLN_TERM = "select main.LN_TERM, catLNComer.LN_COMER_DES from test as main
        join ln_comer as catLNComer on main.LN_TERM = catLNComer.LN_COMER";

        $queryLN_TARJ = "select main.LN_TARJ, catLNTarj.LN_TARJ_DES from test as main
        join ln_tarj as catLNTarj on main.LN_TARJ = catLNTarj.LN_TARJ";

        /*
        Detectar cual de los filtros está siendo utilizados.
        Se incermenta la variable $numberFilters para ingresar al switch
        y se configura la bandera para saber cual de estos filtros son utilizados.
        */
        if (!empty($kq2)) { $numberFilters++; $flagkq2 = true; }
        if (!empty($codeResponse)) { $numberFilters++; $flagCode = true; }
        if (!empty($entryMode)) { $numberFilters++; $flagEntry = true; }

        switch ($numberFilters) {
            case 1: { //Un solo filtro utilizado
                    if ($flagkq2) { //Filtrado por medio de Acceso
                        for ($i = 0; $i < count($kq2); $i++) {
                            $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                            $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ, " where KQ2_ID_MEDIO_ACCESO = ?",
                            $kq2, [], [], $i, 0, 0, $numberFilters);
                        }
                    }
                    if ($flagCode) { //Filtrado por código de respuesta
                        for ($i = 0; $i < count($codeResponse); $i++) {
                            $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER,
                            $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ, " where CODIGO_RESPUESTA = ?",
                            [], $codeResponse, [], $i, 0, 0, $numberFilters);
                        }
                    }
                    if ($flagEntry) { //Filtrado por entry mode
                        for ($i = 0; $i < count($entryMode); $i++) {
                            $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER,
                            $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ, " where ENTRY_MODE = ?",
                            [], [], $entryMode, $i, 0, 0, $numberFilters);
                        }
                    }
                    break;
                }
            case 2: { //Dos filtros utilizados
                    if ($flagkq2) {
                        if($flagCode && !$flagEntry){
                            $firstLength = max($kq2, $codeResponse);
                            switch($firstLength){
                                case $kq2:{
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($codeResponse); $j++){
                                            $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                            $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                            and CODIGO_RESPUESTA = ?", $kq2, $codeResponse, [], $i, $j, 0, $numberFilters);
                                        }
                                    }
                                    break;
                                }
                                case $codeResponse:{
                                    for($i = 0; $i < count($codeResponse); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                            $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                            and CODIGO_RESPUESTA = ?", $kq2, $codeResponse, [], $j, $i, 0, $numberFilters);
                                        }
                                    }
                                    break;
                                }
                            } 
                        }else{
                            $firstLength = max($kq2, $entryMode);
                            switch($firstLength){
                                case $kq2:{
                                    for($i = 0; $i < count($kq2); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                            $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                            and ENTRY_MODE = ?", $kq2, [], $entryMode, $i, $j, 0, $numberFilters);
                                        }
                                    }
                                    break;
                                }
                                case $entryMode:{
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($kq2); $j++){
                                            $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                            $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                            and ENTRY_MODE = ?", $kq2, [], $entryMode, $j, $i, 0, $numberFilters);
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                    }else{
                        if($flagCode & $flagEntry){
                            $firstLength = max($codeResponse, $entryMode);
                            switch($firstLength){
                                case $codeResponse:{
                                    for($i = 0; $i < count($codeResponse); $i++){
                                        for($j = 0; $j < count($entryMode); $j++){
                                            $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                            $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where CODIGO_RESPUESTA = ? 
                                            and ENTRY_MODE = ?", [], $codeResponse, $entryMode, $i, $j, 0, $numberFilters);
                                        }
                                    }
                                    break;
                                }
                                case $entryMode: {
                                    for($i = 0; $i < count($entryMode); $i++){
                                        for($j = 0; $j < count($codeResponse); $j++){
                                            $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                            $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where CODIGO_RESPUESTA = ? 
                                            and ENTRY_MODE = ?", [], $codeResponse, $entryMode, $j, $i, 0, $numberFilters);
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    break;
                }
            case 3: { //Los tres filtros son utilizados
                    if ($flagkq2 && $flagCode && $flagEntry) {
                        $firstLength = max($kq2, $codeResponse, $entryMode);
                        switch($firstLength){
                            case $kq2:{
                                $secondLength = max($codeResponse, $entryMode);
                                switch($secondLength){
                                    case $codeResponse:{
                                        for($i = 0; $i < count($kq2); $i++){
                                            for($j = 0; $j < count($codeResponse); $j++){
                                                for($z = 0; $z < count($entryMode); $z++){
                                                    $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                                    $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                                    and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", $kq2, $codeResponse, $entryMode, 
                                                    $i, $j, $z, $numberFilters);
                                                }
                                            }
                                        }
                                        break;
                                    }
                                    case $entryMode:{
                                        for($i = 0; $i < count($kq2); $i++){
                                            for($j = 0; $j < count($entryMode); $j++){
                                                for($z = 0; $z < count($codeResponse); $z++){
                                                    $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                                    $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                                    and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", $kq2, $codeResponse, $entryMode, 
                                                    $i, $z, $j, $numberFilters);
                                                }
                                            }
                                        }
                                        break;
                                    }
                                }
                                break;
                            }
                            case $codeResponse: {
                                $secondLength = max($kq2, $entryMode);
                                switch($secondLength){
                                    case $kq2:{
                                        for($i = 0; $i < count($codeResponse); $i++){
                                            for($j = 0; $j < count($kq2); $j++){
                                                for($z = 0; $z < count($entryMode); $z++){
                                                    $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                                    $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                                    and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", $kq2, $codeResponse, $entryMode, 
                                                    $j, $i, $z, $numberFilters);
                                                }
                                            }
                                        }
                                        break;
                                    }
                                    case $entryMode:{
                                        for($i = 0; $i < count($codeResponse); $i++){
                                            for($j = 0; $j < count($entryMode); $j++){
                                                for($z = 0; $z < count($kq2); $z++){
                                                    $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                                    $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                                    and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", $kq2, $codeResponse, $entryMode, 
                                                    $z, $i, $j, $numberFilters);
                                                }
                                            }
                                        }
                                        break;
                                    }
                                }
                                break;
                            }
                            case $entryMode: {
                                $secondLength = max($kq2, $codeResponse);
                                switch($secondLength){
                                    case $kq2:{
                                        for($i = 0; $i < count($entryMode); $i++){
                                            for($j = 0; $j < count($kq2); $j++){
                                                for($z = 0; $z < count($codeResponse); $z++){
                                                    $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                                    $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                                    and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", $kq2, $codeResponse, $entryMode, 
                                                    $j, $z, $i, $numberFilters);
                                                }
                                            }
                                        }
                                        break;
                                    }
                                    case $codeResponse:{
                                        for($i = 0; $i < count($entryMode); $i++){
                                            for($j = 0; $j < count($codeResponse); $j++){
                                                for($z = 0; $z < count($kq2); $z++){
                                                    $answer = $this -> getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                                                    $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ," where KQ2_ID_MEDIO_ACCESO = ? 
                                                    and CODIGO_RESPUESTA = ? and ENTRY_MODE = ?", $kq2, $codeResponse, $entryMode, 
                                                    $z, $j, $i, $numberFilters);
                                                }
                                            }
                                        }
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                    }
                    break;
                }
            default: {
                    $answer = $this -> getResultsOutFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, 
                    $queryFIID_TERM, $queryFIID_TARJ, $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ);
                    break;
                } 
        }
        $arrayJson = json_decode(json_encode($answer), true); //Codificar a un array asociativo
        return $arrayJson;

    }
    function getResultsOutFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, $queryFIID_TERM, $queryFIID_TARJ, 
    $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ){
        $z = 0; //Variable para controlar las asignaciones de stdClass para la creación 
        //de objetos dentro del arreglo 'answer' 
        //Consulta para ID_COMER
        $responseSub = DB::select($queryID_COMER);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR)); //Quitar valores repetidos
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->ID_Comer = $data['ID_COMER'];
            $z++;
        }
        //Consulta para TERM_COMER
        $responseSub = DB::select($queryTERM_COMER);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Term_Comer = $data['TERM_COMER'];
            $z++;
        }
        //Consulta para FIID_COMER
        $responseSub = DB::select($queryFIID_COMER);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Fiid_Comer = $data['FIID_COMER'];
            $answer[$z]->Fiid_Comer_Des = $data['FIID_COMER_DES'];
            $z++;
        }
        //Consulta para FIID_TERM
        $responseSub = DB::select($queryFIID_TERM);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Fiid_Term = $data['FIID_TERM'];
            $answer[$z]->Fiid_Term_Des = $data['FIID_COMER_DES'];
            $z++;
        }
        //Consulta para FIID_TARJ
        $responseSub = DB::select($queryFIID_TARJ);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Fiid_Tarj = $data['FIID_TARJ'];
            $answer[$z]->Fiid_Tarj_Des = $data['FIID_TARJ_DES'];
            $z++;
        }
        //Consulta para LN_COMER
        $responseSub = DB::select($queryLN_COMER);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Ln_Comer = $data['LN_COMER'];
            $answer[$z]->Ln_Comer_Des = $data['LN_COMER_DES'];
            $z++;
        }
        //Consulta para LN_TERM
        $responseSub = DB::select($queryLN_TERM);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Ln_Term = $data['LN_TERM'];
            $answer[$z]->Ln_Term_Des = $data['LN_COMER_DES'];
            $z++;
        }
        //Consulta para LN_TARJ
        $responseSub = DB::select($queryLN_TARJ);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach($arrayClened as $key => $data){
            $answer[$z] = new stdClass();
            $answer[$z]->Ln_Tarj = $data['LN_TARJ'];
            $answer[$z]->Ln_Tarj_Des = $data['LN_TARJ_DES'];
            $z++;
        }
        return $answer;
    }

    function getResultsFilters($queryID_COMER, $queryTERM_COMER, $queryFIID_COMER, $queryFIID_TERM, $queryFIID_TARJ, 
    $queryLN_COMER, $queryLN_TERM, $queryLN_TARJ, $restOfQuery, $kq2, $codeResponse, $entryMode, $i, $j, $z, $numberFilters){
        $counter = 0; //Variable para controlar las asignaciones de stdClass para la creación 
        //de objetos dentro del arreglo 'answer'
        $responseSub = array();
        $answer = array();
        //Consulta para ID_COMER
        $responseSub = $this -> getData($kq2, $codeResponse, $entryMode, $queryID_COMER, $restOfQuery, $i, $j, $z, $numberFilters);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR)); //Quitar valores repetidos
        foreach ($arrayClened as $key => $data) {
            $answer[$counter] = new stdClass();
            $answer[$counter]->ID_Comer = $data['ID_COMER'];
            $counter++;
        }
        //Consulta para TERM_COMER
        $responseSub = $this -> getData($kq2, $codeResponse, $entryMode, $queryTERM_COMER, $restOfQuery, $i, $j, $z, $numberFilters);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$counter] = new stdClass();
            $answer[$counter]->Term_Comer = $data['TERM_COMER'];
            $counter++;
        }
        //Consulta para FIID_COMER
        $responseSub = $this -> getData($kq2, $codeResponse, $entryMode, $queryFIID_COMER, $restOfQuery, $i, $j, $z, $numberFilters);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$counter] = new stdClass();
            $answer[$counter]->Fiid_Comer = $data['FIID_COMER'];
            $answer[$counter]->Fiid_Comer_Des = $data['FIID_COMER_DES'];
            $counter++;
        }
        //Consulta para FIID_TERM
        $responseSub = $this -> getData($kq2, $codeResponse, $entryMode, $queryFIID_TERM, $restOfQuery, $i, $j, $z, $numberFilters);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$counter] = new stdClass();
            $answer[$counter]->Fiid_Term = $data['FIID_TERM'];
            $answer[$counter]->Fiid_Term_Des = $data['FIID_COMER_DES'];
            $counter++;
        }
        //Consulta para FIID_TARJ
        $responseSub = $this -> getData($kq2, $codeResponse, $entryMode, $queryFIID_TARJ, $restOfQuery, $i, $j, $z, $numberFilters);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$counter] = new stdClass();
            $answer[$counter]->Fiid_Tarj = $data['FIID_TARJ'];
            $answer[$counter]->Fiid_Tarj_Des = $data['FIID_TARJ_DES'];
            $counter++;
        }
        //Consulta para LN_COMER
        $responseSub = $this -> getData($kq2, $codeResponse, $entryMode, $queryLN_COMER, $restOfQuery, $i, $j, $z, $numberFilters);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$counter] = new stdClass();
            $answer[$counter]->Ln_Comer = $data['LN_COMER'];
            $answer[$counter]->Ln_Comer_Des = $data['LN_COMER_DES'];
            $counter++;
        }
        //Consulta para LN_TERM
        $responseSub = $this -> getData($kq2, $codeResponse, $entryMode, $queryLN_TERM, $restOfQuery, $i, $j, $z, $numberFilters);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$counter] = new stdClass();
            $answer[$counter]->Ln_Term = $data['LN_TERM'];
            $answer[$counter]->Ln_Term_Des = $data['LN_COMER_DES'];
            $counter++;
        }
        //Consulta para LN_TARJ
        $responseSub = $this -> getData($kq2, $codeResponse, $entryMode, $queryLN_TARJ, $restOfQuery, $i, $j, $z, $numberFilters);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach($arrayClened as $key => $data){
            $answer[$counter] = new stdClass();
            $answer[$counter]->Ln_Tarj = $data['LN_TARJ'];
            $answer[$counter]->Ln_Tarj_Des = $data['LN_TARJ_DES'];
            $counter++;
        }
        return $answer;
    }

    function getData($kq2, $codeResponse, $entryMode, $query, $restOfQuery, $i, $j, $z, $numberFilters){
        $responseSub = array();
        switch($numberFilters){
            case 1:{
                if(!empty($kq2)){
                    $responseSub = array_merge($responseSub, DB::select($query.$restOfQuery, [$kq2[$i]]));
                }
                if(!empty($codeResponse)){
                    $responseSub = array_merge($responseSub, DB::select($query.$restOfQuery, [$codeResponse[$i]]));
                }
                if(!empty($entryMode)){
                    $responseSub = array_merge($responseSub, DB::select($query.$restOfQuery, [$entryMode[$i]]));
                }
                break;
            }
            case 2:{
                if(!empty($kq2)){
                    if(!empty($codeResponse) && empty($entryMode)){
                        $responseSub = array_merge($responseSub, DB::select($query.$restOfQuery, [$kq2[$i], $codeResponse[$j]]));
                    }else{
                        $responseSub = array_merge($responseSub, DB::select($query.$restOfQuery, [$kq2[$i], $entryMode[$j]]));
                    }
                }else{
                    if(!empty($codeResponse) && !empty($entryMode)){
                        $responseSub = array_merge($responseSub, DB::select($query.$restOfQuery, [$codeResponse[$i], $entryMode[$j]]));
                    }
                }
                break;
            }
            case 3:{
                $responseSub = array_merge($responseSub, DB::select($query.$restOfQuery, [$kq2[$i], $codeResponse[$j], $entryMode[$z]]));
                break;
            }
        }
        return $responseSub;
    }

    public function getCatalogs(){

        $queryFIID_COMER = "select main.FIID_COMER, catComer.FIID_COMER_DES from test as main
        join fiid_comer as catComer on main.FIID_COMER = catComer.FIID_COMER";

        $queryFIID_TARJ = "select main.FIID_TARJ, catTarj.FIID_TARJ_DES from test as main 
        join fiid_tarj as catTarj on main.FIID_TARJ = catTarj.FIID_TARJ";

        $queryFIID_TERM = "select main.FIID_TERM, catComer.FIID_COMER_DES from test as main 
        join fiid_comer as catComer on main.FIID_COMER = catComer.FIID_COMER";

        $queryLN_COMER = "select main.LN_COMER, catLNComer.LN_COMER_DES from test as main 
        join ln_comer as catLNComer on main.LN_COMER = catLNComer.LN_COMER";

        $queryLN_TERM = "select main.LN_TERM, catLNComer.LN_COMER_DES from test as main
        join ln_comer as catLNComer on main.LN_TERM = catLNComer.LN_COMER";

        $queryLN_TARJ = "select main.LN_TARJ, catLNTarj.LN_TARJ_DES from test as main
        join ln_tarj as catLNTarj on main.LN_TARJ = catLNTarj.LN_TARJ";
        $z = 0;

        //Consulta para FIID_COMER
        $responseSub = DB::select($queryFIID_COMER);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Fiid_Comer = $data['FIID_COMER'];
            $answer[$z]->Fiid_Comer_Des = $data['FIID_COMER_DES'];
            $z++;
        }
        //Consulta para FIID_TERM
        $responseSub = DB::select($queryFIID_TERM);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Fiid_Term = $data['FIID_TERM'];
            $answer[$z]->Fiid_Term_Des = $data['FIID_COMER_DES'];
            $z++;
        }
        //Consulta para FIID_TARJ
        $responseSub = DB::select($queryFIID_TARJ);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Fiid_Tarj = $data['FIID_TARJ'];
            $answer[$z]->Fiid_Tarj_Des = $data['FIID_TARJ_DES'];
            $z++;
        }
        //Consulta para LN_COMER
        $responseSub = DB::select($queryLN_COMER);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Ln_Comer = $data['LN_COMER'];
            $answer[$z]->Ln_Comer_Des = $data['LN_COMER_DES'];
            $z++;
        }
        //Consulta para LN_TERM
        $responseSub = DB::select($queryLN_TERM);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach ($arrayClened as $key => $data) {
            $answer[$z] = new stdClass();
            $answer[$z]->Ln_Term = $data['LN_TERM'];
            $answer[$z]->Ln_Term_Des = $data['LN_COMER_DES'];
            $z++;
        }
        //Consulta para LN_TARJ
        $responseSub = DB::select($queryLN_TARJ);
        $array = json_decode(json_encode($responseSub), true);
        $arrayClened = array_values(array_unique($array, SORT_REGULAR));
        foreach($arrayClened as $key => $data){
            $answer[$z] = new stdClass();
            $answer[$z]->Ln_Tarj = $data['LN_TARJ'];
            $answer[$z]->Ln_Tarj_Des = $data['LN_TARJ_DES'];
            $z++;
        }
        return $answer;
    }
}
