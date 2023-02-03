<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashKMN(Request $request)
    {
        $values = array();
        $valuesExtra = array();
        $labels = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'ID_ITEM', 'LN_TARJ', 'FIID_TARJ', 'NUMERO_DE_TARJETA', 'LN_COMER',
        'FIID_COMER', 'REGION', 'ID_COMER', 'TERM_COMER', 'LN_TERM', 'FIID_TERM', 'TIPO', 'O', 'R', 'NUM_SEC', 'NOMBRE_DE_TERMINAL', 'T_CNTRY', 
        'NUM_INST_ADQ', 'NUM_INST_EMI', 'TIPO_TERM', 'SIC_CDE', 'TIPO_TRANSAC', 'TARJ_ASOCIADA', 'CTA_ASOCIADA', 'C', 'TIPO_TARJETA', 'CODIGO_APROV',
        'DFT_CAPTURE', 'COD_REVERSO', 'MONEDA', 'CURRENCY', 'PREFIJO6'];
        
        $values[0] = $request -> kq2;
        $values[1] = $request -> codeResp;
        $values[2] = $request -> entryMode;
        $values[3] = $request -> idItem;
        $values[4] = $request -> ln_card;
        $values[5] = $request -> fiid_card;
        $values[6] = $request -> num_card;
        $values[7] = $request -> ln_comer;
        $values[8] = $request -> fiid_comer;
        $values[9] = $request -> region;
        $values[10] = $request -> id_comer;
        $values[11] = $request -> term_comer;
        $values[12] = $request -> ln_term;
        $values[13] = $request -> fiid_term;
        $values[14] = $request -> tipo;
        $values[15] = $request -> o;
        $values[16] = $request -> r;
        $values[17] = $request -> num_sec;
        $values[18] = $request -> term_name;
        $values[19] = $request -> tcntry;
        $values[20] = $request -> numInstAdq;
        $values[21] = $request -> numInstemi;
        $values[22] = $request -> termtype;
        $values[23] = $request -> sicode;
        $values[24] = $request -> txType;
        $values[25] = $request -> tarjAs;
        $values[26] = $request -> ctAs;
        $values[27] = $request -> c;
        $values[28] = $request -> cardType;
        $values[29] = $request -> aprovCode;
        $values[30] = $request -> ft;
        $values[31] = $request -> codeRev;
        $values[32] = $request -> money;
        $values[33] = $request -> currency;
        $values[34] = $request -> pre6;

        $valuesExtra[0] = $request -> startDate;
        $valuesExtra[1] = $request -> finishDate;
        $valuesExtra[2] = $request -> startHour;
        $valuesExtra[3] = $request -> finishHour; 

        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM,
        LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, sum(MONTO1) AS MONTO, count(*) as TXS, FECHA_TRANS, HORA_TRANS, ID_ITEM, TIPO_TARJETA,
        NUMERO_DE_TARJETA, REGION, O, R, ENTRY_T, EXIT_T, RE_ENTRY_T, FECHA_TRANS, HORA_TRANS, FECHA_POSTEO, T_CNTRY, NUM_INST_ADQ, NUM_INST_EMI, TIPO_TERM,
        SIC_CDE, TIPO_TRANSAC, TARJ_ASOCIADA, CTA_ASOCIADA, C, TIPO_TARJETA, MONTO1, MONTO2, CODIGO_APROV, DFT_CAPTURE, COD_REVERSO, MONEDA, CURRENCY, PREFIJO6,
        TIPO, NUM_SEC, NOMBRE_DE_TERMINAL, FECHA_Y_HORA 
        from ".$request -> bd." group by KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM,
        LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, FECHA_TRANS, HORA_TRANS, ID_ITEM, TIPO_TARJETA,
        NUMERO_DE_TARJETA, REGION, O, R, ENTRY_T, EXIT_T, RE_ENTRY_T, FECHA_TRANS, HORA_TRANS, FECHA_POSTEO, T_CNTRY, NUM_INST_ADQ, NUM_INST_EMI, TIPO_TERM,
        SIC_CDE, TIPO_TRANSAC, TARJ_ASOCIADA, CTA_ASOCIADA, C, TIPO_TARJETA, MONTO1, MONTO2, CODIGO_APROV, DFT_CAPTURE, COD_REVERSO, MONEDA, CURRENCY, PREFIJO6,
        TIPO, NUM_SEC, NOMBRE_DE_TERMINAL, FECHA_Y_HORA having ";

        $queryOutFilters = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM,
        LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, sum(MONTO1) AS MONTO, count(*) as TXS, FECHA_TRANS, HORA_TRANS, ID_ITEM, TIPO_TARJETA,
        NUMERO_DE_TARJETA, REGION, O, R, ENTRY_T, EXIT_T, RE_ENTRY_T, FECHA_TRANS, HORA_TRANS, FECHA_POSTEO, T_CNTRY, NUM_INST_ADQ, NUM_INST_EMI, TIPO_TERM,
        SIC_CDE, TIPO_TRANSAC, TARJ_ASOCIADA, CTA_ASOCIADA, C, TIPO_TARJETA, MONTO1, MONTO2, CODIGO_APROV, DFT_CAPTURE, COD_REVERSO, MONEDA, CURRENCY, PREFIJO6,
        TIPO, NUM_SEC, NOMBRE_DE_TERMINAL, FECHA_Y_HORA from ".$request -> bd." group by KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM,
        LN_COMER, LN_TERM, FIID_TARJ, LN_TARJ, FECHA_TRANS, HORA_TRANS, ID_ITEM, TIPO_TARJETA,
        NUMERO_DE_TARJETA, REGION, O, R, ENTRY_T, EXIT_T, RE_ENTRY_T, FECHA_TRANS, HORA_TRANS, FECHA_POSTEO, T_CNTRY, NUM_INST_ADQ, NUM_INST_EMI, TIPO_TERM,
        SIC_CDE, TIPO_TRANSAC, TARJ_ASOCIADA, CTA_ASOCIADA, C, TIPO_TARJETA, MONTO1, MONTO2, CODIGO_APROV, DFT_CAPTURE, COD_REVERSO, MONEDA, CURRENCY, PREFIJO6, 
        TIPO, NUM_SEC, NOMBRE_DE_TERMINAL, FECHA_Y_HORA";

        $queryDateTime = " and (FECHA_TRANS >= ? and FECHA_TRANS <= ?) and (HORA_TRANS >= ? and HORA_TRANS <= ?)";
        $queryDateTimeOutFilters = " having (FECHA_TRANS >= ? and FECHA_TRANS <= ?) and (HORA_TRANS >= ? and HORA_TRANS <= ?)";
        $response = array();
        $array = array();
        $arrayValues = array();

        //Eliminar los values y los arrays que no se estén utilizando
        for($key = 0; $key < 35; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);

        if(empty($filteredValues)){
            //return 'a';
            $response = DB::select($queryOutFilters.$queryDateTimeOutFilters, [...$valuesExtra]);
            $array = json_decode(json_encode($response), true);
        }else{
                //Ingresar todos los valores elegidos en el filtro dentro de un solo arreglo. (Valores para la consulta)
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        array_push($arrayValues, $filteredValues[$i][$j]);
                    }
                }
                $z = 1; //Variable 'controladora' de el largo del query
                //Constructor del query (Varias consultas al mismo tiempo)
                for($i = 0; $i < count($filteredValues); $i++){
                    for($j = 0; $j < count($filteredValues[$i]); $j++){
                        if($j == count($filteredValues[$i]) -1){
                            if($j == 0){
                                if($z == count($arrayValues)){
                                    $query .= "(".$filteredLabels[$i]." = ?)";
                                }else{
                                    $query .= "(".$filteredLabels[$i]." = ?) and ";
                                }
                                $z++;
                            }else{
                                if($z == count($arrayValues)){
                                    $query .= $filteredLabels[$i]." = ?)";
                                    $z = 1;
                                }else{
                                    $query .= $filteredLabels[$i]." = ?) and ";
                                    $z++;
                                }
                            }
                        }else{
                            if($j == 0){
                                $query .= "(".$filteredLabels[$i]." = ? or ";
                                $z++;
                            }else{
                                $query .= $filteredLabels[$i]." = ? or ";
                                $z++;
                            }
                        }
                    }
                }
                //return $query.$queryDateTime;
                //Consulta del query obtenido por los filtros y los valores elegidos
                $response = DB::select($query.$queryDateTime, [...$arrayValues, ...$valuesExtra]);
                $array = json_decode(json_encode($response), true);
        }
        $answer = array();
        foreach($array as $key => $data){
            $answer[$key] = new stdClass();
            $answer[$key] -> code_Response = $data['CODIGO_RESPUESTA'];
            $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
            $answer[$key] -> entry_Mode = $data['ENTRY_MODE'];
            $answer[$key] -> type = $data['TIPO'];
            $answer[$key] -> idItem = $data['ID_ITEM'];
            $answer[$key] -> dateTimeFront = $data['FECHA_Y_HORA'];
            $answer[$key] -> cardNumber = $data['NUMERO_DE_TARJETA'];
            $answer[$key] -> region = $data['REGION'];
            $answer[$key] -> o = $data['O'];
            $answer[$key] -> r = $data['R'];
            $answer[$key] -> entryT = $data['ENTRY_T'];
            $answer[$key] -> exitT = $data['EXIT_T'];
            $answer[$key] -> rEntryT = $data['RE_ENTRY_T'];
            $answer[$key] -> dateTrans = $data['FECHA_TRANS'];
            $answer[$key] -> timeTrans = $data['HORA_TRANS'];
            $answer[$key] -> datePost = $data['FECHA_POSTEO'];
            $answer[$key] -> tCntry = $data['T_CNTRY'];
            $answer[$key] -> numInstAdq = $data['NUM_INST_ADQ'];
            $answer[$key] -> numInstEmi = $data['NUM_INST_EMI'];
            $answer[$key] -> termType = $data['TIPO_TERM'];
            $answer[$key] -> sic_cde = $data['SIC_CDE'];
            $answer[$key] -> txType = $data['TIPO_TRANSAC'];
            $answer[$key] -> asCard = $data['TARJ_ASOCIADA'];
            $answer[$key] -> asAcount = $data['CTA_ASOCIADA'];
            $answer[$key] -> c = $data['C'];
            $answer[$key] -> cardType = $data['TIPO_TARJETA'];
            $answer[$key] -> amount1 = $data['MONTO1'];
            $answer[$key] -> amount2 = $data['MONTO2'];
            $answer[$key] -> aprovCode = $data['CODIGO_APROV'];
            $answer[$key] -> dft = $data['DFT_CAPTURE'];
            $answer[$key] -> codeRev = $data['COD_REVERSO'];
            $answer[$key] -> money = $data['MONEDA'];
            $answer[$key] -> currency = $data['CURRENCY'];
            $answer[$key] -> pre6 = $data['PREFIJO6'];
            $answer[$key]->ID_Comer = $data['ID_COMER'];
            $answer[$key]->Term_Comer = $data['TERM_COMER'];
            $answer[$key]->Fiid_Comer = $data['FIID_COMER'];
            $answer[$key]->Fiid_Term = $data['FIID_TERM'];
            $answer[$key]->Ln_Comer = $data['LN_COMER'];
            $answer[$key]->Ln_Term = $data['LN_TERM'];
            $answer[$key]->Fiid_Card = $data['FIID_TARJ'];
            $answer[$key]->Ln_Card = $data['LN_TARJ'];
            $answer[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $answer[$key]->Number_Sec = $data['NUM_SEC'];
            //Separación decimal y entero del monto para agregar el punto
            $dec = substr($data["MONTO"], strlen($data['MONTO'])-2, 2);
            $int = substr($data['MONTO'], 0, strlen($data['MONTO'])-2);
            $answer[$key] -> amount = $int.".".$dec;
            $answer[$key] -> tx = $data['TXS'];
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        return $arrayJSON;
    }

    public function dashPTLF(Request $request){
        $values = array();
        $dash = array();
        $values[0] = $request -> kq2;
        $values[1] = $request -> codeResponse;
        $values[2] = $request -> entryMode;
        
        $queryOutFilters = 'select TKN_Q2_ID_ACCESO, RESPUESTA, PEM, COMERCIO, TERM_ID, EMISOR, ADQUIRENTE,
        LN, RED, sum(MONTO) AS MONTO, count(*) as TXS from '.$request -> bd.' group by TKN_Q2_ID_ACCESO, RESPUESTA, 
        PEM, COMERCIO, TERM_ID, EMISOR, ADQUIRENTE, LN, RED';

        $response = DB::select($queryOutFilters);
        $datajson = json_decode(json_encode($response), true);

        foreach($datajson as $key => $data){
            $dash[$key] = new stdClass();
            $dash[$key] -> code_Response = $data['RESPUESTA'];
            $dash[$key] -> ID_Access_Mode = $data['TKN_Q2_ID_ACCESO'];
            $dash[$key] -> entry_Mode = $data['PEM'];
            $dash[$key] -> amount = $data['MONTO'];
            $dash[$key] -> tx = $data['TXS'];
            $dash[$key] -> Fiid_Comer = $data['EMISOR'];
            $dash[$key] -> Fiid_Term = $data['EMISOR'];
            $dash[$key] -> Fiid_Tarj = $data['ADQUIRENTE'];
            $dash[$key] -> Ln_Comer = $data['LN'];
            $dash[$key] -> Ln_Term = $data['LN'];
            $dash[$key] -> Ln_Tarj = $data['RED'];
        }
        $arrayJSON = json_decode(json_encode($dash), true);
        return $arrayJSON;
    }
}
