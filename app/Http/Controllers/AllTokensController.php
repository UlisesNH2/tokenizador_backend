<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class AllTokensController extends Controller
{
    public function getKm(Request $request){
        $dataElements = array();
        $responseTB2 = array(); $responseTB3 = array(); $responseTB4 = array();
        $responseTB5 = array(); $responseTB6 = array();
        $responseTC0 = array(); $responseTC4 = array();
        $responseTQ9 = array(); $responseTQR = array();
        $responseTQ1 = array(); $responseTEZ = array();
        $responseES = array();
        $values = array();
        $arrayValues = array();
        $queryOutFilters = "select * from ".$request -> bd;

        $query = $queryOutFilters." where ";

        $labels = ['KQ2_ID_MEDIO_ACCESO', 'CODIGO_RESPUESTA', 'ENTRY_MODE', 'ID_ITEM', 'LN_TARJ', 'FIID_TARJ', 'NUMERO_DE_TARJETA', 'LN_COMER',
        'FIID_COMER', 'REGION', 'ID_COMER', 'TERM_COMER', 'LN_TERM', 'FIID_TERM', 'TIPO', 'O', 'R', 'NUM_SEC', 'NOMBRE_DE_TERMINAL', 'T_CNTRY', 
        'NUM_INST_ADQ', 'NUM_INST_EMI', 'TIPO_TERM', 'SEC_CDE', 'TIPO_TRANSAC', 'TARJ_ASOCIADA', 'CTA_ASOCIADA', 'C', 'TIPO_TARJETA', 'CODIGO_APROV',
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

        //Detectar cuales son los filtros utilizados
        for($key = 0; $key < 35; $key++){
            if(empty($values[$key])){
                unset($values[$key]);
                unset($labels[$key]);
            }
        }
        $filteredValues = array_values($values);
        $filteredLabels = array_values($labels);

        for($i = 0; $i < count($filteredValues); $i++){
            for($j = 0; $j < count($filteredValues[$i]); $j++){
                if($filteredValues[$i][$j] === null){
                    $filteredValues[$i][$j] = " ";
                }
            }
        }

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
            $dataElements[$key] = new stdClass();
            $dataElements[$key] -> idQ2 = 'Q2';
            $dataElements[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO']; 
            $dataElements[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $dataElements[$key] -> entryMode = $data['ENTRY_MODE'];
            $dataElements[$key] -> type = $data['TIPO'];
            $dataElements[$key] -> idItem = $data['ID_ITEM'];
            $dataElements[$key] -> dateTimeFront = $data['FECHA_Y_HORA'];
            $dataElements[$key] -> cardNumber = $data['NUMERO_DE_TARJETA'];
            $dataElements[$key] -> region = $data['REGION'];
            $dataElements[$key] -> o = $data['O'];
            $dataElements[$key] -> r = $data['R'];
            $dataElements[$key] -> entryT = $data['ENTRY_T'];
            $dataElements[$key] -> exitT = $data['EXIT_T'];
            $dataElements[$key] -> rEntryT = $data['RE_ENTRY_T'];
            $dataElements[$key] -> dateTrans = $data['FECHA_TRANS'];
            $dataElements[$key] -> timeTrans = $data['HORA_TRANS'];
            $dataElements[$key] -> datePost = $data['FECHA_POSTEO'];
            $dataElements[$key] -> tCntry = $data['T_CNTRY'];
            $dataElements[$key] -> numInstAdq = $data['NUM_INST_ADQ'];
            $dataElements[$key] -> numInstEmi = $data['NUM_INST_EMI'];
            $dataElements[$key] -> termType = $data['TIPO_TERM'];
            $dataElements[$key] -> sic_cde = $data['SIC_CDE'];
            $dataElements[$key] -> txType = $data['TIPO_TRANSAC'];
            $dataElements[$key] -> asCard = $data['TARJ_ASOCIADA'];
            $dataElements[$key] -> asAcount = $data['CTA_ASOCIADA'];
            $dataElements[$key] -> c = $data['C'];
            $dataElements[$key] -> cardType = $data['TIPO_TARJETA'];
            $dataElements[$key] -> amount1 = $data['MONTO1'];
            $dataElements[$key] -> amount2 = $data['MONTO2'];
            $dataElements[$key] -> aprovCode = $data['CODIGO_APROV'];
            $dataElements[$key] -> dft = $data['DFT_CAPTURE'];
            $dataElements[$key] -> codeRev = $data['COD_REVERSO'];
            $dataElements[$key] -> money = $data['MONEDA'];
            $dataElements[$key] -> currency = $data['CURRENCY'];
            $dataElements[$key] -> pre6 = $data['PREFIJO6'];
            $dataElements[$key]->ID_Comer = $data['ID_COMER'];
            $dataElements[$key]->Term_Comer = $data['TERM_COMER'];
            $dataElements[$key]->Fiid_Comer = $data['FIID_COMER'];
            $dataElements[$key]->Fiid_Term = $data['FIID_TERM'];
            $dataElements[$key]->Ln_Comer = $data['LN_COMER'];
            $dataElements[$key]->Ln_Term = $data['LN_TERM'];
            $dataElements[$key]->Fiid_Card = $data['FIID_TARJ'];
            $dataElements[$key]->Ln_Card = $data['LN_TARJ'];
            $dataElements[$key]->Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
            $dataElements[$key]->Number_Sec = $data['NUM_SEC'];
        }

        //Token B2
        foreach($datajson as $key => $data){
            $responseTB2[$key] = new stdClass();
            $responseTB2[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTB2[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTB2[$key] -> entryMode = $data['ENTRY_MODE'];
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
        foreach($datajson as $key => $data){
            $responseTQ9[$key] = new stdClass();
            $responseTQ9[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTQ9[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTQ9[$key] -> entryMode = $data['ENTRY_MODE'];
            //Token Q9
            $responseTQ9[$key] -> idTokenQ9 = 'Q9';
            $responseTQ9[$key] -> orPem = $data['KQ9_ORIGINAL_POST_ENTRY_MODE'];
            $responseTQ9[$key] -> cdeSer = $data['KQ9_SERVICE_CODE'];
            $responseTQ9[$key] -> arqcVerq9 = $data['KQ9_ARQC_VERIFY'];
        }
        foreach($datajson as $key => $data){
            $responseTQR[$key] = new stdClass();
            $responseTQR[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTQR[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTQR[$key] -> entryMode = $data['ENTRY_MODE'];
            //Token QR
            $responseTQR[$key] -> idTokenQR = 'QR';
            $responseTQR[$key] -> cardTpDesc = $data['KQR_CRD_TYP_DESC'];
            $responseTQR[$key] -> cardDesc = $data['KQR_CRD_DESC'];
            $responseTQR[$key] -> issDesc = $data['KQR_ISS_DESC'];
        }
        foreach($datajson as $key => $data){
            $responseTQ1[$key] = new stdClass();
            $responseTQ1[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTQ1[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTQ1[$key] -> entryMode = $data['ENTRY_MODE'];
            //Token Q1
            $responseTQ1[$key] -> idTokenQ1 = 'Q1';
            $responseTQ1[$key] -> idMode = $data['KQ1_MODO_INDENTIFICADOR'];
        }
        foreach($datajson as $key => $data){
            $responseTEZ[$key] = new stdClass();
            $responseTEZ[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTEZ[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTEZ[$key] -> entryMode = $data['ENTRY_MODE'];
            //Token EZ
            $responseTEZ[$key] -> idTokenEZ = 'EZ';
            $responseTEZ[$key] -> ksn = $data['KEZ_KSN'];
            $responseTEZ[$key] -> crypCnt = $data['KEZ_ENCRYPT_CNT'];
            $responseTEZ[$key] -> cryptFail = $data['KEZ_ENCRYPT_FAIL_CNT'];
            $responseTEZ[$key] -> tk3Fl = $data['KEZ_TRACK3_FLG'];
            $responseTEZ[$key] -> EZPem = $data['KEZ_ENTRY_MDE'];
            $responseTEZ[$key] -> tk2Len = $data['KEZ_TRACK2_LGTH'];
            $responseTEZ[$key] -> cvv2Flag = $data['KEZ_CVV2_FLG'];
            $responseTEZ[$key] -> cvv2Len = $data['KEZ_CVV2_LGTH'];
            $responseTEZ[$key] -> tk1Fl = $data['KEZ_TRACK1_FLG'];
            $responseTEZ[$key] -> crypBuff = $data['KEZ_ENCRYPT_BUFFER'];
            $responseTEZ[$key] -> panl4 = $data['KEZ_PAN_LAST4'];
            $responseTEZ[$key] -> crc = $data['KEZ_ENCRYPT_BUFFER_CRC32'];
        }
        foreach($datajson as $key => $data){
            $responseTES[$key] = new stdClass();
            $responseTES[$key] -> kq2 = $data['KQ2_ID_MEDIO_ACCESO'];
            $responseTES[$key] -> codeResp = $data['CODIGO_RESPUESTA'];
            $responseTES[$key] -> entryMode = $data['ENTRY_MODE'];
            //Token ES
            $responseTES[$key] -> idTokenES = 'ES';
            $responseTES[$key] -> softVer = $data['KES_SOFTWARE_VER'];
            $responseTES[$key] -> pinPad = $data['KES_PIN_PAD_SERIAL_NUM'];
            $responseTES[$key] -> crypType = $data['KES_ENCRYPT_TYP'];
            $responseTES[$key] -> binNxt = $data['KES_BIN_TBL_ID_NXT'];
            $responseTES[$key] -> binCur = $data['KES_BIN_TBL_ID_CUR'];
            $responseTES[$key] -> binVer = $data['KES_BIN_TBL_VER'];
            $responseTES[$key] -> newKeyfl = $data['KES_NEW_KEY_FLG'];
        }

        $response = array();
        $response = new stdClass();
        $response -> dataEs = json_decode(json_encode($dataElements), true);
        $response -> tokenB2 = json_decode(json_encode($responseTB2), true);
        $response -> tokenB3 = json_decode(json_encode($responseTB3), true);
        $response -> tokenB4 = json_decode(json_encode($responseTB4), true);
        $response -> tokenB5 = json_decode(json_encode($responseTB5), true);
        $response -> tokenB6 = json_decode(json_encode($responseTB6), true);
        $response -> tokenC0 = json_decode(json_encode($responseTC0), true);
        $response -> tokenC4 = json_decode(json_encode($responseTC4), true);
        $response -> tokenQ9 = json_decode(json_encode($responseTQ9), true);
        $response -> tokenQR = json_decode(json_encode($responseTQR), true);
        $response -> tokenQ1 = json_decode(json_encode($responseTQ1), true);
        $response -> tokenEZ = json_decode(json_encode($responseTEZ), true);
        $response -> tokenES = json_decode(json_encode($responseTES), true);

        $responseJson = json_decode(json_encode($response), true);
        return $responseJson;
    }

    public function getPTLF(Request $request){

        $responseTC0 = array(); $responseTCZ = array(); $responseTC4 = array();
        $responseTB2 = array(); $responseTB3 = array();
        $responseTB4 = array(); $responseTB5 = array();
        $responseTB6 = array(); $values = array();
        $dataElements = array();

        $labels = [
            'TKN_Q2_ID_ACCESO', 
            'RESPUESTA', 
            'PEM', 
            'MENSAJE', 
            'LN', 
            'EMISOR', 
            'RED', 
            'ADQUIRENTE',
            'RVRL_CDE',
            'OPERACION',
            'IND_CASHBACK',
            'DFC',
            'TERM_ID',
            'RESPONDER',
            'T',
            'COMERCIO',
            'TERM_CITY',
            'GIRO',
            'APROBACION',
            '(select substring(TKN_C4, 11, 1))', '(select substring(TKN_C4, 12, 1))', '(select substring(TKN_C4, 13, 1))',
            '(select substring(TKN_C4, 14, 1))', '(select substring(TKN_C4, 15, 1))', '(select substring(TKN_C4, 16, 1))',
            '(select substring(TKN_C4, 17, 1))', '(select substring(TKN_C4, 18, 1))', '(select substring(TKN_C4, 19, 1))',
            '(select substring(TKN_C4, 20, 1))', '(select substring(TKN_C4, 21, 1))', '(select substring(TKN_C4, 22, 1))'];

        $arrayValues = array();

        $queryOutFilters = "select * from ".$request -> bd;

        $query = "select * from ".$request -> bd." where ";
        //Data elements
        $values[0] = $request -> kq2; 
        $values[1] = $request -> codeResponse;
        $values[2] = $request -> entryMode;
        $values[3] = $request -> mess;
        $values[4] = $request -> ln;
        $values[5] = $request -> trans;
        $values[6] = $request -> network;
        $values[7] = $request -> adq;
        $values[8] = $request -> rvl;
        $values[9] = $request -> op;
        $values[10] = $request -> cashbk;
        $values[11] = $request -> dfc;
        $values[12] = $request -> termId;
        $values[13] = $request -> responder;
        $values[14] = $request -> t;
        $values[15] = $request -> comer;
        $values[16] = $request -> termcty;
        $values[17] = $request -> giro;
        $values[18] = $request -> aprov;
        //tokenB2
        $values[19] = $request -> idTermAtt;
        $values[20] = $request -> idTerm;
        $values[21] = $request -> termLoc;
        $values[22] = $request -> chPres;
        $values[23] = $request -> cardPres;
        $values[24] = $request -> cardCap;
        $values[25] = $request -> status;
        $values[26] = $request -> secLevel;
        $values[27] = $request -> idRouting;
        $values[28] = $request -> termActCh;
        $values[29] = $request -> termDataTrans;
        $values[30] = $request -> chMeth;

        //Eliminar filtros no seleccionados
        for($key = 0; $key < 31; $key++){
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
            //return $query;
            $response = DB::select($query, [...$arrayValues]);
            $datajson = json_decode(json_encode($response), true);
        }

        foreach($datajson as $key => $data){
            $dataElements[$key] = new stdClass();
            $dataElements[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO'];
            $dataElements[$key] -> entryMode = $data['PEM'];
            $dataElements[$key] -> codeResp = $data['RESPUESTA'];
            $dataElements[$key] -> op = $data['OPERACION'];
            $dataElements[$key] -> mess = $data['MENSAJE'];
            $dataElements[$key] -> sec = $data['SECUENCIA'];
            $dataElements[$key] -> ln = $data['LN'];
            $dataElements[$key] -> trans = $data['EMISOR'];
            $dataElements[$key] -> network = $data['RED'];
            $dataElements[$key] -> adq = $data['ADQUIRENTE'];
            $dataElements[$key] -> aprov = $data['APROBACION'];
            $dataElements[$key] -> pre = $data['PREFIJO'];
            //
            $dataElements[$key] -> numCard = $data['NUM_TARJETA'];
            $dataElements[$key] -> afi = $data['AFILIACION'];
            $dataElements[$key] -> date = $data['FECHA'];
            $dataElements[$key] -> time = $data['HORA'];
            $dataElements[$key] -> sec = $data['SECUENCIA'];
            $dataElements[$key] -> amount1 = $data['MONTO'];
            $dataElements[$key] -> amount2 = $data['MONTO2'];
            $dataElements[$key] -> rvrl = $data['RVRL_CDE'];
            $dataElements[$key] -> cashbk = $data['IND_CASHBACK'];
            $dataElements[$key] -> dfc = $data['DFC'];
            $dataElements[$key] -> termId = $data['TERM_ID'];
            $dataElements[$key] -> responder = $data['RESPONDER'];
            $dataElements[$key] -> t = $data['T'];
            $dataElements[$key] -> comer = $data['COMERCIO'];
            $dataElements[$key] -> termcty = $data['TERM_CITY'];
            $dataElements[$key] -> giro = $data['GIRO']; //Pendiente consultar con catálogo
        }

        foreach($datajson as $key => $data){
            $responseTC0[$key] = new stdClass();
            $responseTC0[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO'];
            $responseTC0[$key] -> entryMode = $data['PEM'];
            $responseTC0[$key] -> codeResp = $data['RESPUESTA'];
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
            //Token C4
            $responseTC4[$key] -> idTokenC4 = 'C4';
            $responseTC4[$key] -> c4 = $data['TKN_C4'];
        }
        foreach($datajson as $key => $data){
            $responseTCZ[$key] = new stdClass();
            $responseTCZ[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO'];
            $responseTCZ[$key] -> entryMode = $data['PEM'];
            $responseTCZ[$key] -> codeResp = $data['RESPUESTA'];
            //tokenCZ
            $responseTCZ[$key] -> idTokenCZ = 'CZ';
            $responseTCZ[$key] -> cz = $data['TKN_CZ'];
        }
        foreach($datajson as $key => $data){
            $responseTB2[$key] = new stdClass();
            $responseTB2[$key] -> kq2 = $data['TKN_Q2_ID_ACCESO'];
            $responseTB2[$key] -> entryMode = $data['PEM'];
            $responseTB2[$key] -> codeResp = $data['RESPUESTA'];
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
            //Token B6
            $responseTB6[$key] -> idTokenB6 = 'B6';
            $responseTB6[$key] -> issScripDataLen = $data['B6_ISS_SCRIPT_DATA_LGTH'];
            $responseTB6[$key] -> issScripData = $data['B6_ISS_SCRIPT_DATA'];
        }

        $response = new stdClass();
        $response -> dataElements = json_decode(json_encode($dataElements), true);
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
