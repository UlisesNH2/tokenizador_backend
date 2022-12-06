<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class AllTokensController extends Controller
{
    public function getKm(Request $request){

        $responseTB2 = array(); $responseTB3 = array(); $responseTB4 = array();
        $responseTB5 = array(); $responseTB6 = array();
        $responseTC0 = array(); $responseTC4 = array();
        $query = "select KQ2_ID_MEDIO_ACCESO, CODIGO_RESPUESTA, ENTRY_MODE, TIPO, MONTO1,
        ID_COMER, TERM_COMER, FIID_COMER, FIID_TERM, LN_COMER,
        LN_TERM, FIID_TARJ, LN_TARJ, NOMBRE_DE_TERMINAL, NUM_SEC,
        KB2_BIT_MAP,KB2_USR_FLD1,KB2_ARQC,KB2_AMT_AUTH,KB2_AMT_OTHER, KB2_ATC,KB2_TERM_CTRY_CDE,
        KB2_TRAN_CRNCY_CDE,KB2_TRAN_DAT,KB2_TRAN_TYPE,KB2_UNPREDICT_NUM,KB2_ISS_APPL_DATA_LGTH,KB2_ISS_APPL_DATA,
        KB2_CRYPTO_INFO_DATA,KB2_TVR,KB2_AIP, KB3_BIT_MAP, KB3_TERM_SRL_NUM, KB3_EMV_TERM_CAP, KB3_USR_FLD1, 
        KB3_USR_FLD2, KB3_EMV_TERM_TYPE, KB3_APP_VER_NUM, KB3_CVM_RSLTS, KB3_DF_NAME_LGTH, KB3_DF_NAME, 
        KB4_PT_SRV_ENTRY_MDE, KB4_TERM_ENTRY_CAP, KB4_LAST_EMV_STAT, KB4_DATA_SUSPECT,
        KB4_APPL_PAN_SEQ_NUM, KB4_DEV_INFO, KB4_RSN_ONL_CDE, KB4_ARQC_VRFY, KB4_ISO_RC_IND,
        KB5_ISS_AUTH_DATA_LGTH, KB5_ARPC, KB5_CRD_STAT_UPDT, KB5_ADDL_DATA, KB5_SEND_CRD_BLK, KB5_SEND_PUT_DATA,
        KB6_ISS_SCRIPT_DATA_LGTH, KB6_ISS_SCRIPT_DATA,
        KC0_INDICADOR_DE_COMERCIO_ELEC,KC0_TIPO_DE_TARJETA, KC0_INDICADOR_DE_CVV2_CVC2_PRE, KC0_INDICADOR_DE_INFORMACION_A,
        KC4_TERM_ATTEND_IND,KC4_TERM_OPER_IND,KC4_TERM_LOC_IND,
        KC4_CRDHLDR_PRESENT_IND,KC4_CRD_PRESENT_IND,KC4_CRD_CAPTR_IND,KC4_TXN_STAT_IND,KC4_TXN_SEC_IND,KC4_TXN_RTN_IND,
        KC4_CRDHLDR_ACTVT_TERM_IND,KC4_TERM_INPUT_CAP_IND,KC4_CRDHLDR_ID_METHOD from ".$request -> bd;


        $data = DB::select($query);
        $datajson = json_decode(json_encode($data), true);

        //Token B2
        foreach($datajson as $key => $data){
            $responseTB2[$key] = new stdClass();
            $responseTB2[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTB2[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTB2[$key] -> entryMode = $data['ENTRY_MODE'];
            $responseTB2[$key] -> type = $data['TIPO'];
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $responseTB2[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
            $responseTB2[$key]->ID_Comer = $data['ID_COMER'];
            $responseTB2[$key]->Term_Comer = $data['TERM_COMER'];
            $responseTB2[$key]->Fiid_Comer = $data['FIID_COMER'];
            $responseTB2[$key]->Fiid_Term = $data['FIID_TERM'];
            $responseTB2[$key]->Ln_Comer = $data['LN_COMER'];
            $responseTB2[$key]->Ln_Term = $data['LN_TERM'];
            $responseTB2[$key]->Fiid_Card = $data['FIID_TARJ'];
            $responseTB2[$key]->Ln_Card = $data['LN_TARJ'];
            $responseTB2[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $responseTB2[$key]->Number_Sec = $data['NUM_SEC'];
            //Token B2
            $responseTB2[$key] -> idTokenB2 = 'B2';
            $responseTB2[$key] -> bitMapB2 = $data['KB2_BIT_MAP'];
            $responseTB2[$key] -> UsrFO = $data['KB2_USR_FLD1'];
            $responseTB2[$key] -> CrypData = $data['KB2_CRYPTO_INFO_DATA'];
            $responseTB2[$key] -> ARQC = $data['KB2_ARQC'];
            $responseTB2[$key] -> AMTAuth = $data['KB2_AMT_AUTH'];
            $responseTB2[$key] -> AMTOther = $data['KB2_AMT_OTHER'];
            $responseTB2[$key] -> ATC = $data['KB2_ATC'];
            $responseTB2[$key] -> TermCounCode = $data['KB2_TERM_CTRY_CDE'];
            $responseTB2[$key] -> TermCurrCode = $data['KB2_TRAN_CRNCY_CDE'];
            $responseTB2[$key] -> TranDate = $data['KB2_TRAN_DAT'];
            $responseTB2[$key] -> TranType = $data['KB2_TRAN_TYPE'];
            $responseTB2[$key] -> UmpNum = $data['KB2_UNPREDICT_NUM'];
            $responseTB2[$key] -> IssAppDataLen = $data['KB2_ISS_APPL_DATA_LGTH'];
            $responseTB2[$key] -> IssAppData = $data['KB2_ISS_APPL_DATA'];
            $responseTB2[$key] -> TVR = $data['KB2_TVR'];
            $responseTB2[$key] -> AIP = $data['KB2_AIP']; //16
        }

        foreach($datajson as $key => $data){
            $responseTB3[$key] = new stdClass();
            $responseTB3[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTB3[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTB3[$key] -> entryMode = $data['ENTRY_MODE'];
            $responseTB3[$key] -> type = $data['TIPO'];
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $responseTB3[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
            $responseTB3[$key]->ID_Comer = $data['ID_COMER'];
            $responseTB3[$key]->Term_Comer = $data['TERM_COMER'];
            $responseTB3[$key]->Fiid_Comer = $data['FIID_COMER'];
            $responseTB3[$key]->Fiid_Term = $data['FIID_TERM'];
            $responseTB3[$key]->Ln_Comer = $data['LN_COMER'];
            $responseTB3[$key]->Ln_Term = $data['LN_TERM'];
            $responseTB3[$key]->Fiid_Card = $data['FIID_TARJ'];
            $responseTB3[$key]->Ln_Card = $data['LN_TARJ'];
            $responseTB3[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $responseTB3[$key]->Number_Sec = $data['NUM_SEC'];
            //Token B3
            $responseTB3[$key]->idTokenB3 = 'B3';
            $responseTB3[$key]->bitMap = $data['KB3_BIT_MAP'];
            $responseTB3[$key]->TermNum = $data['KB3_TERM_SRL_NUM'];
            $responseTB3[$key]->CheckCh = $data['KB3_EMV_TERM_CAP'];
            $responseTB3[$key]->UsrFOne = $data['KB3_USR_FLD1'];
            $responseTB3[$key]->UsrFTwo = $data['KB3_USR_FLD2'];
            $responseTB3[$key]->TermTpEMV = $data['KB3_EMV_TERM_TYPE'];
            $responseTB3[$key]->AppVerNum = $data['KB3_APP_VER_NUM'];
            $responseTB3[$key]->CVMRes = $data['KB3_CVM_RSLTS'];
            $responseTB3[$key]->fileNameLen = $data['KB3_DF_NAME_LGTH'];
            $responseTB3[$key]->fileName = $data['KB3_DF_NAME']; //10
        }

        foreach($datajson as $key => $data){
            $responseTB4[$key] = new stdClass();
            $responseTB4[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTB4[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTB4[$key] -> entryMode = $data['ENTRY_MODE'];
            $responseTB4[$key] -> type = $data['TIPO'];
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $responseTB4[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
            $responseTB4[$key]->ID_Comer = $data['ID_COMER'];
            $responseTB4[$key]->Term_Comer = $data['TERM_COMER'];
            $responseTB4[$key]->Fiid_Comer = $data['FIID_COMER'];
            $responseTB4[$key]->Fiid_Term = $data['FIID_TERM'];
            $responseTB4[$key]->Ln_Comer = $data['LN_COMER'];
            $responseTB4[$key]->Ln_Term = $data['LN_TERM'];
            $responseTB4[$key]->Fiid_Card = $data['FIID_TARJ'];
            $responseTB4[$key]->Ln_Card = $data['LN_TARJ'];
            $responseTB4[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $responseTB4[$key]->Number_Sec = $data['NUM_SEC'];
            //Token B4
            $responseTB4[$key]->idTokenB4 = 'B4';
            $responseTB4[$key]->SerEntryMode = $data['KB4_PT_SRV_ENTRY_MDE'];
            $responseTB4[$key]->CapTerm = $data['KB4_TERM_ENTRY_CAP'];
            $responseTB4[$key]->EVMSts = $data['KB4_LAST_EMV_STAT'];
            $responseTB4[$key]->DataSus = $data['KB4_DATA_SUSPECT'];
            $responseTB4[$key]->PANum = $data['KB4_APPL_PAN_SEQ_NUM'];
            $responseTB4[$key]->DevInfo = $data['KB4_DEV_INFO'];
            $responseTB4[$key]->OnlCode = $data['KB4_RSN_ONL_CDE'];
            $responseTB4[$key]->ARQCVer = $data['KB4_ARQC_VRFY'];
            $responseTB4[$key]->RespISO = $data['KB4_ISO_RC_IND'];
        }

        foreach($datajson as $key => $data){
            $responseTB5[$key] = new stdClass();
            $responseTB5[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTB5[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTB5[$key] -> entryMode = $data['ENTRY_MODE'];
            $responseTB5[$key] -> type = $data['TIPO'];
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $responseTB5[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
            $responseTB5[$key]->ID_Comer = $data['ID_COMER'];
            $responseTB5[$key]->Term_Comer = $data['TERM_COMER'];
            $responseTB5[$key]->Fiid_Comer = $data['FIID_COMER'];
            $responseTB5[$key]->Fiid_Term = $data['FIID_TERM'];
            $responseTB5[$key]->Ln_Comer = $data['LN_COMER'];
            $responseTB5[$key]->Ln_Term = $data['LN_TERM'];
            $responseTB5[$key]->Fiid_Card = $data['FIID_TARJ'];
            $responseTB5[$key]->Ln_Card = $data['LN_TARJ'];
            $responseTB5[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $responseTB5[$key]->Number_Sec = $data['NUM_SEC'];
            //Token B5
            $responseTB5[$key] -> idTokenB5 = 'B5';
            $responseTB5[$key] -> issAuthDataLen = $data['KB5_ISS_AUTH_DATA_LGTH'];
            $responseTB5[$key] -> ARPC = $data['KB5_ARPC'];
            $responseTB5[$key] -> Card_update = $data['KB5_CRD_STAT_UPDT'];
            $responseTB5[$key] -> ADDLdata = $data['KB5_ADDL_DATA'];
            $responseTB5[$key] -> sendCrdBlk = $data['KB5_SEND_CRD_BLK'];
            $responseTB5[$key] -> sendPutData = $data['KB5_SEND_PUT_DATA'];
        }

        foreach($datajson as $key => $data){
            $responseTB6[$key] = new stdClass();
            $responseTB6[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTB6[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTB6[$key] -> entryMode = $data['ENTRY_MODE'];
            $responseTB6[$key] -> type = $data['TIPO'];
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $responseTB6[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
            $responseTB6[$key]->ID_Comer = $data['ID_COMER'];
            $responseTB6[$key]->Term_Comer = $data['TERM_COMER'];
            $responseTB6[$key]->Fiid_Comer = $data['FIID_COMER'];
            $responseTB6[$key]->Fiid_Term = $data['FIID_TERM'];
            $responseTB6[$key]->Ln_Comer = $data['LN_COMER'];
            $responseTB6[$key]->Ln_Term = $data['LN_TERM'];
            $responseTB6[$key]->Fiid_Card = $data['FIID_TARJ'];
            $responseTB6[$key]->Ln_Card = $data['LN_TARJ'];
            $responseTB6[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $responseTB6[$key]->Number_Sec = $data['NUM_SEC'];
            //TOKEN B6
            $responseTB6[$key] -> idTokenB6 = 'B6';
            $responseTB6[$key] -> issScripDataLen = $data['KB6_ISS_SCRIPT_DATA_LGTH'];
            $responseTB6[$key] -> issScripData = $data['KB6_ISS_SCRIPT_DATA'];
        }

        foreach($datajson as $key => $data){
            $responseTC0[$key] = new stdClass();
            $responseTC0[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTC0[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTC0[$key] -> entryMode = $data['ENTRY_MODE'];
            $responseTC0[$key] -> type = $data['TIPO'];
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $responseTC0[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
            $responseTC0[$key]->ID_Comer = $data['ID_COMER'];
            $responseTC0[$key]->Term_Comer = $data['TERM_COMER'];
            $responseTC0[$key]->Fiid_Comer = $data['FIID_COMER'];
            $responseTC0[$key]->Fiid_Term = $data['FIID_TERM'];
            $responseTC0[$key]->Ln_Comer = $data['LN_COMER'];
            $responseTC0[$key]->Ln_Term = $data['LN_TERM'];
            $responseTC0[$key]->Fiid_Card = $data['FIID_TARJ'];
            $responseTC0[$key]->Ln_Card = $data['LN_TARJ'];
            $responseTC0[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $responseTC0[$key]->Number_Sec = $data['NUM_SEC'];
            //Token C0
            $responseTC0[$key] -> idTokenC0 = 'C0';
            $responseTC0[$key]->ecommerce = $data['KC0_INDICADOR_DE_COMERCIO_ELEC'];
            $responseTC0[$key]->cardtp = $data['KC0_TIPO_DE_TARJETA'];
            $responseTC0[$key]->cvv2 = $data['KC0_INDICADOR_DE_CVV2_CVC2_PRE'];
            $responseTC0[$key]->info = $data['KC0_INDICADOR_DE_INFORMACION_A'];
        }

        foreach($datajson as $key => $data){
            $responseTC4[$key] = new stdClass();
            $responseTC4[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTC4[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTC4[$key] -> entryMode = $data['ENTRY_MODE'];
            $responseTC4[$key] -> type = $data['TIPO'];
            $dec = substr($data['MONTO1'], strlen($data['MONTO1']) -2, 2);
            $int = substr($data['MONTO1'], 0, strlen($data['MONTO1']) -2);
            $responseTC4[$key] -> amount = '$'.number_format($int.'.'.$dec, 2);
            $responseTC4[$key]->ID_Comer = $data['ID_COMER'];
            $responseTC4[$key]->Term_Comer = $data['TERM_COMER'];
            $responseTC4[$key]->Fiid_Comer = $data['FIID_COMER'];
            $responseTC4[$key]->Fiid_Term = $data['FIID_TERM'];
            $responseTC4[$key]->Ln_Comer = $data['LN_COMER'];
            $responseTC4[$key]->Ln_Term = $data['LN_TERM'];
            $responseTC4[$key]->Fiid_Card = $data['FIID_TARJ'];
            $responseTC4[$key]->Ln_Card = $data['LN_TARJ'];
            $responseTC4[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $responseTC4[$key]->Number_Sec = $data['NUM_SEC'];
            //Token C4
            $responseTC4[$key] -> idTokenC4 = 'C4';
            $responseTC4[$key]->idTermAt = $data['KC4_TERM_ATTEND_IND']; //subcampo 1
            $responseTC4[$key]->idTerm = $data['KC4_TERM_OPER_IND']; //subcampo 2
            $responseTC4[$key]->termLoc = $data['KC4_TERM_LOC_IND']; //subcampo 3
            $responseTC4[$key]->chPres = $data['KC4_CRDHLDR_PRESENT_IND']; //subcampo 4
            $responseTC4[$key]->cardPres = $data['KC4_CRD_PRESENT_IND']; //subcampo 5
            $responseTC4[$key]->cardCap = $data['KC4_CRD_CAPTR_IND']; //subcampo 6
            $responseTC4[$key]->status = $data['KC4_TXN_STAT_IND']; //subcampo 7
            $responseTC4[$key]->secLevel = $data['KC4_TXN_SEC_IND']; //subcampo 8
            $responseTC4[$key]->idRouting = $data['KC4_TXN_RTN_IND']; //subcampo 9
            $responseTC4[$key]->termActCh = $data['KC4_CRDHLDR_ACTVT_TERM_IND']; //subcampo 10
            $responseTC4[$key]->termDataTrans = $data['KC4_TERM_INPUT_CAP_IND']; //subcampo 11
            $responseTC4[$key]->chMeth = $data['KC4_CRDHLDR_ID_METHOD']; // subcampo 12
        }

        $response = array();
        $response = new stdClass();
        $response -> tokenB2 = json_decode(json_encode($responseTB2), true);
        $response -> tokenB3 = json_decode(json_encode($responseTB3), true);
        $response -> tokenB4 = json_decode(json_encode($responseTB4), true);
        $response -> tokenB5 = json_decode(json_encode($responseTB5), true);
        $response -> tokenB6 = json_decode(json_encode($responseTB6), true);
        $response -> tokenC0 = json_decode(json_encode($responseTC0), true);
        $response -> tokenC4 = json_decode(json_encode($responseTC4), true);

        $responseJson = json_decode(json_encode($response), true);
        return $responseJson;
    }

    public function getPTLF(Request $request){

        $responseTC0 = array(); $responseTCZ = array(); $responseTC4 = array();
        $responseTB2 = array(); $responseTB3 = array();
        $responseTB4 = array(); $responseTB5 = array();
        $responseTB6 = array(); $values = array();

        $labels = ['TKN_Q2_ID_ACCESO', 'RESPUESTA', 'PEM'];

        $arrayValues = array();

        $queryOutFilters = "select TKN_Q2_ID_ACCESO, PEM, RESPUESTA, MENSAJE, SECUENCIA, LN, ADQUIRENTE, EMISOR, RED, APROBACION,
        C0_05, C0_06, C0_07, C0_08, TKN_CZ, TKN_C4,
        B2_BIT_MAP, B2_USER_FLD1, B2_CRYPTO_INFO_DATA, B2_ARQC, B2_AMT_AUTH, B2_AMT_OTHER,
        B2_ATC, B2_TERM_CNTRY_CDE, B2_TRAN_CRNCY_CDE, B2_TRAN_DAT, B2_TRAN_TYPE, B2_UNPREDICT_NUM, 
        B2_ISS_APPL_DATA_LGTH, B2_ISS_APPL_DATA, B2_TVR, B2_AIP,
        B3_BIT_MAP, B3_TERM_SERL_NUM, B3_EMV_TERM_CAP, B3_USER_FLD1, 
        B3_USER_FLD2, B3_EMV_TERM_TYPE, B3_APPL_VER_NUM, B3_CVM_RSLTS, 
        B3_DF_NAME_LGTH, B3_DF_NAME, 
        B4_PT_SRV_ENTRY_MDE, B4_TERM_ENTRY_CAP, B4_LAST_EMV_STAT, 
        B4_DATA_SUSPECT, B4_APPL_PAN_SEQ_NUM, B4_DEV_INFO, 
        B4_RSN_ONL_CDE, B4_ARQC_VRFY, B4_USER_FLD1,
        B5_ISS_AUTH_DATA_LGTH, B5_ARPC, B5_ADDL_DATA, B5_SEND_CRD_BLK, 
        B5_SEND_PUT_DATA,
        B6_ISS_SCRIPT_DATA_LGTH, B6_ISS_SCRIPT_DATA from ".$request -> bd;

        $query = "select TKN_Q2_ID_ACCESO, PEM, RESPUESTA, MENSAJE, SECUENCIA, LN, ADQUIRENTE, EMISOR, RED, APROBACION,
        C0_05, C0_06, C0_07, C0_08, TKN_CZ, TKN_C4,
        B2_BIT_MAP, B2_USER_FLD1, B2_CRYPTO_INFO_DATA, B2_ARQC, B2_AMT_AUTH, B2_AMT_OTHER,
        B2_ATC, B2_TERM_CNTRY_CDE, B2_TRAN_CRNCY_CDE, B2_TRAN_DAT, B2_TRAN_TYPE, B2_UNPREDICT_NUM, 
        B2_ISS_APPL_DATA_LGTH, B2_ISS_APPL_DATA, B2_TVR, B2_AIP,
        B3_BIT_MAP, B3_TERM_SERL_NUM, B3_EMV_TERM_CAP, B3_USER_FLD1, 
        B3_USER_FLD2, B3_EMV_TERM_TYPE, B3_APPL_VER_NUM, B3_CVM_RSLTS, 
        B3_DF_NAME_LGTH, B3_DF_NAME, 
        B4_PT_SRV_ENTRY_MDE, B4_TERM_ENTRY_CAP, B4_LAST_EMV_STAT, 
        B4_DATA_SUSPECT, B4_APPL_PAN_SEQ_NUM, B4_DEV_INFO, 
        B4_RSN_ONL_CDE, B4_ARQC_VRFY, B4_USER_FLD1,
        B5_ISS_AUTH_DATA_LGTH, B5_ARPC, B5_ADDL_DATA, B5_SEND_CRD_BLK, 
        B5_SEND_PUT_DATA,
        B6_ISS_SCRIPT_DATA_LGTH, B6_ISS_SCRIPT_DATA from ".$request -> bd." where ";

        $values[0] = $request -> kq2; 
        $values[1] = $request -> codeResponse;
        $values[2] = $request -> entryMode;

        //Eliminar filtros no seleccionados
        for($key = 0; $key < 3; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);

        if(empty($filteredValues)){
            $response = DB::select($queryOutFilters);
            $datajson = json_decode(json_encode($response), true);
        }else{
            //Ingresar todos los valores de $filteredValues en un solo arreglo
            for ($i = 0; $i < count($filteredValues); $i++) {
                for ($j = 0; $j < count($filteredValues[$i]); $j++) {
                    array_push($arrayValues, $filteredValues[$i][$j]);
                }
            }
            $z = 1; //Variable para el control de la longitud del query
            //Construcción del query de acuerdo a los filtros seleccionados
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
            $response = DB::select($query, [...$arrayValues]);
            $datajson = json_decode(json_encode($response), true);
        }

        foreach($datajson as $key => $data){
            $responseTC0[$key] = new stdClass();
            $responseTC0[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO'];
            $responseTC0[$key] -> entryMode = $data['PEM'];
            $responseTC0[$key] -> codeResp = $data['RESPUESTA'];
            $responseTC0[$key] -> mess = $data['MENSAJE'];
            $responseTC0[$key] -> sec = $data['SECUENCIA'];
            $responseTC0[$key] -> ln = $data['LN'];
            $responseTC0[$key] -> trans = $data['EMISOR'];
            $responseTC0[$key] -> network = $data['RED'];
            $responseTC0[$key] -> adq = $data['ADQUIRENTE'];
            $responseTC0[$key] -> aprov = $data['APROBACION'];
            //Token C0
            $responseTC0[$key] -> idTokenC0 = 'C0';
            $responseTC0[$key] -> ecommerce = $data['C0_05'];
            $responseTC0[$key] -> cardtp = $data['C0_06'];
            $responseTC0[$key] -> cvv2 = $data['C0_08'];
            $responseTC0[$key] -> info = $data['C0_07'];
        }
        foreach($datajson as $key => $data){
            $responseTC4[$key] = new stdClass();
            $responseTC4[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO'];
            $responseTC4[$key] -> entryMode = $data['PEM'];
            $responseTC4[$key] -> codeResp = $data['RESPUESTA'];
            $responseTC4[$key] -> mess = $data['MENSAJE'];
            $responseTC4[$key] -> sec = $data['SECUENCIA'];
            $responseTC4[$key] -> ln = $data['LN'];
            $responseTC4[$key] -> trans = $data['EMISOR'];
            $responseTC4[$key] -> network = $data['RED'];
            $responseTC4[$key] -> adq = $data['ADQUIRENTE'];
            $responseTC4[$key] -> aprov = $data['APROBACION'];
            //Token C4
            $responseTC4[$key] -> idTokenC4 = 'C4';
            $responseTC4[$key] -> c4 = $data['TKN_C4'];
        }
        foreach($datajson as $key => $data){
            $responseTCZ[$key] = new stdClass();
            $responseTCZ[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO'];
            $responseTCZ[$key] -> entryMode = $data['PEM'];
            $responseTCZ[$key] -> codeResp = $data['RESPUESTA'];
            $responseTCZ[$key] -> mess = $data['MENSAJE'];
            $responseTCZ[$key] -> sec = $data['SECUENCIA'];
            $responseTCZ[$key] -> ln = $data['LN'];
            $responseTCZ[$key] -> trans = $data['EMISOR'];
            $responseTCZ[$key] -> network = $data['RED'];
            $responseTCZ[$key] -> adq = $data['ADQUIRENTE'];
            $responseTCZ[$key] -> aprov = $data['APROBACION'];
            //tokenCZ
            $responseTCZ[$key] -> idTokenCZ = 'CZ';
            $responseTCZ[$key] -> cz = $data['TKN_CZ'];
        }
        foreach($datajson as $key => $data){
            $responseTB2[$key] = new stdClass();
            $responseTB2[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO'];
            $responseTB2[$key] -> entryMode = $data['PEM'];
            $responseTB2[$key] -> codeResp = $data['RESPUESTA'];
            $responseTB2[$key] -> mess = $data['MENSAJE'];
            $responseTB2[$key] -> sec = $data['SECUENCIA'];
            $responseTB2[$key] -> ln = $data['LN'];
            $responseTB2[$key] -> trans = $data['EMISOR'];
            $responseTB2[$key] -> network = $data['RED'];
            $responseTB2[$key] -> adq = $data['ADQUIRENTE'];
            $responseTB2[$key] -> aprov = $data['APROBACION'];
            //tokenB2
            $responseTB2[$key] -> idTokenB2 = 'B2';
            $responseTB2[$key] -> bitMapB2 = $data['B2_BIT_MAP'];
            $responseTB2[$key] -> UsrFO = $data['B2_USER_FLD1'];
            $responseTB2[$key] -> CrypData = $data['B2_CRYPTO_INFO_DATA'];
            $responseTB2[$key] -> ARQC = $data['B2_ARQC'];
            $responseTB2[$key] -> AMTAuth = $data['B2_AMT_AUTH'];
            $responseTB2[$key] -> AMTOther = $data['B2_AMT_OTHER'];
            $responseTB2[$key] -> ATC = $data['B2_ATC'];
            $responseTB2[$key] -> TermCounCode = $data['B2_TERM_CNTRY_CDE'];
            $responseTB2[$key] -> TermCurrCode = $data['B2_TRAN_CRNCY_CDE'];
            $responseTB2[$key] -> TranDate = $data['B2_TRAN_DAT'];
            $responseTB2[$key] -> TranType = $data['B2_TRAN_TYPE'];
            $responseTB2[$key] -> UmpNum = $data['B2_UNPREDICT_NUM'];
            $responseTB2[$key] -> IssAppDataLen = $data['B2_ISS_APPL_DATA_LGTH'];
            $responseTB2[$key] -> IssAppData = $data['B2_ISS_APPL_DATA'];
            $responseTB2[$key] -> TVR = $data['B2_TVR'];
            $responseTB2[$key] -> AIP = $data['B2_AIP'];
        }
        foreach($datajson as $key => $data){
            $responseTB3[$key] = new stdClass();
            $responseTB3[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO'];
            $responseTB3[$key] -> entryMode = $data['PEM'];
            $responseTB3[$key] -> codeResp = $data['RESPUESTA'];
            $responseTB3[$key] -> mess = $data['MENSAJE'];
            $responseTB3[$key] -> sec = $data['SECUENCIA'];
            $responseTB3[$key] -> ln = $data['LN'];
            $responseTB3[$key] -> trans = $data['EMISOR'];
            $responseTB3[$key] -> network = $data['RED'];
            $responseTB3[$key] -> aprov = $data['APROBACION'];
            $responseTB3[$key] -> adq = $data['ADQUIRENTE'];
            //token B3
            $responseTB3[$key] -> idTokenB3 = 'B3';
            $responseTB3[$key]->bitMap = $data['B3_BIT_MAP'];
            $responseTB3[$key]->TermNum = $data['B3_TERM_SERL_NUM'];
            $responseTB3[$key]->CheckCh = $data['B3_EMV_TERM_CAP'];
            $responseTB3[$key]->UsrFOne = $data['B3_USER_FLD1'];
            $responseTB3[$key]->UsrFTwo = $data['B3_USER_FLD2'];
            $responseTB3[$key]->TermTpEMV = $data['B3_EMV_TERM_TYPE'];
            $responseTB3[$key]->AppVerNum = $data['B3_APPL_VER_NUM'];
            $responseTB3[$key]->CVMRes = $data['B3_CVM_RSLTS'];
            $responseTB3[$key]->fileNameLen = $data['B3_DF_NAME_LGTH'];
            $responseTB3[$key]->fileName = $data['B3_DF_NAME'];
        }
        foreach($datajson as $key => $data){
            $responseTB4[$key] = new stdClass();
            $responseTB4[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO']; //
            $responseTB4[$key] -> entryMode = $data['PEM'];
            $responseTB4[$key] -> codeResp = $data['RESPUESTA'];
            $responseTB4[$key] -> mess = $data['MENSAJE'];
            $responseTB4[$key] -> sec = $data['SECUENCIA'];
            $responseTB4[$key] -> ln = $data['LN'];
            $responseTB4[$key] -> trans = $data['EMISOR'];
            $responseTB4[$key] -> network = $data['RED'];
            $responseTB4[$key] -> aprov = $data['APROBACION'];
            $responseTB4[$key] -> adq = $data['ADQUIRENTE'];
            //Token B4
            $responseTB4[$key] -> idTokenB4 = 'B4';
            $responseTB4[$key]->SerEntryMode = $data['B4_PT_SRV_ENTRY_MDE'];
            $responseTB4[$key]->CapTerm = $data['B4_TERM_ENTRY_CAP'];
            $responseTB4[$key]->EVMSts = $data['B4_LAST_EMV_STAT'];
            $responseTB4[$key]->DataSus = $data['B4_DATA_SUSPECT'];
            $responseTB4[$key]->PANum = $data['B4_APPL_PAN_SEQ_NUM'];
            $responseTB4[$key]->DevInfo = $data['B4_DEV_INFO'];
            $responseTB4[$key]->OnlCode = $data['B4_RSN_ONL_CDE'];
            $responseTB4[$key]->ARQCVer = $data['B4_ARQC_VRFY'];
            $responseTB4[$key]->RespISO = $data['B4_USER_FLD1'];
        }
        foreach($datajson as $key => $data){
            $responseTB5[$key] = new stdClass();
            $responseTB5[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO']; //
            $responseTB5[$key] -> entryMode = $data['PEM'];
            $responseTB5[$key] -> codeResp = $data['RESPUESTA'];
            $responseTB5[$key] -> mess = $data['MENSAJE'];
            $responseTB5[$key] -> sec = $data['SECUENCIA'];
            $responseTB5[$key] -> ln = $data['LN'];
            $responseTB5[$key] -> trans = $data['EMISOR'];
            $responseTB5[$key] -> network = $data['RED'];
            $responseTB5[$key] -> aprov = $data['APROBACION'];
            $responseTB5[$key] -> adq = $data['ADQUIRENTE'];
            //Token B5
            $responseTB5[$key] -> idTokenB5 = 'B5';
            $responseTB5[$key] -> issAuthDataLen = $data['B5_ISS_AUTH_DATA_LGTH'];
            $responseTB5[$key] -> ARPC = $data['B5_ARPC'];
            $responseTB5[$key] -> Card_update = '';
            $responseTB5[$key] -> ADDLdata = $data['B5_ADDL_DATA'];
            $responseTB5[$key] -> sendCrdBlk = $data['B5_SEND_CRD_BLK'];
            $responseTB5[$key] -> sendPutData = $data['B5_SEND_PUT_DATA'];
        }

        foreach($datajson as $key => $data){
            $responseTB6[$key] = new stdClass();
            $responseTB6[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO']; 
            $responseTB6[$key] -> entryMode = $data['PEM'];
            $responseTB6[$key] -> codeResp = $data['RESPUESTA'];
            $responseTB6[$key] -> mess = $data['MENSAJE'];
            $responseTB6[$key] -> sec = $data['SECUENCIA'];
            $responseTB6[$key] -> ln = $data['LN'];
            $responseTB6[$key] -> trans = $data['EMISOR'];
            $responseTB6[$key] -> network = $data['RED'];
            $responseTB6[$key] -> aprov = $data['APROBACION'];
            $responseTB6[$key] -> adq = $data['ADQUIRENTE'];
            //Token B6
            $responseTB6[$key] -> idTokenB6 = 'B6';
            $responseTB6[$key] -> issScripDataLen = $data['B6_ISS_SCRIPT_DATA_LGTH'];
            $responseTB6[$key] -> issScripData = $data['B6_ISS_SCRIPT_DATA'];
        }

        $response = new stdClass();
        $response -> tokenC0 = json_decode(json_encode($responseTC0), true);
        $response -> tokenC4 = json_decode(json_encode($responseTC4), true);
        $response -> tokenCZ = json_decode(json_encode($responseTCZ), true);
        $response -> tokenB2 = json_decode(json_encode($responseTB2), true);
        $response -> tokenB3 = json_decode(json_encode($responseTB3), true);
        $response -> tokenB4 = json_decode(json_encode($responseTB4), true);
        $response -> tokenB5 = json_decode(json_encode($responseTB5), true);
        $response -> tokenB6 = json_decode(json_encode($responseTB6), true);

        return json_decode(json_encode($response), true);
    }
}
