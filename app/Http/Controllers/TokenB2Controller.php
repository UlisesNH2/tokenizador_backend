<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class TokenB2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $tokenb2 = DB::select("select KB2_BIT_MAP,KB2_USR_FLD1,KB2_ARQC,KB2_AMT_AUTH,KB2_AMT_OTHER,
        KB2_ATC,KB2_TERM_CTRY_CDE,KB2_TRAN_CRNCY_CDE,KB2_TRAN_DAT,KB2_TRAN_TYPE,
        KB2_UNPREDICT_NUM,KB2_ISS_APPL_DATA_LGTH,KB2_ISS_APPL_DATA,
        KB2_CRYPTO_INFO_DATA,KB2_TVR,KB2_AIP from test");
        $array = json_decode(json_encode($tokenb2), true);

        //Número 7 en binario (111) para hacer la comparación
        //con las diversas combinaciones
        $fisrtCombinationNumber = base_convert('7', 10, 2);
        //$secondCombinationNumber = str_pad(base_convert('1',10,2), 2, '0', STR_PAD_LEFT);

        $answer = array();

        foreach($array as $key => $data){
            if(strlen($data['KB2_BIT_MAP']) !== 4){
                $answer[$key] = new stdClass();
                $answer[$key] -> Bit_Map = $data['KB2_BIT_MAP'];
                $answer[$key] -> bitMapFlag = 0;
                $answer[$key] -> User_Field_One = $data['KB2_USR_FLD1'];
                $answer[$key] -> userFOFlag = 0;
                $answer[$key] -> Crypto_Data = $data['KB2_CRYPTO_INFO_DATA'];
                $answer[$key] -> cryptoFlag = 0;
                $answer[$key] -> ARQC = $data['KB2_ARQC'];
                $answer[$key] -> arqcFlag = 0;
                $answer[$key] -> AMT_Auth = $data['KB2_AMT_AUTH'];
                $answer[$key] -> amtAuthFlag = 0;
                $answer[$key] -> AMT_Other = $data['KB2_AMT_OTHER'];
                $answer[$key] -> amtOtherFlag = 0;
                $answer[$key] -> ATC = $data['KB2_ATC'];
                $answer[$key] -> atcFlag = 0;
                $answer[$key] -> Terminal_Country_Code = $data['KB2_TERM_CTRY_CDE'];
                $answer[$key] -> termConFlag = 0;
                $answer[$key] -> Terminal_Currency_Code = $data['KB2_TRAN_CRNCY_CDE'];
                $answer[$key] -> termCurrFlag = 0;
                $answer[$key] -> Transaction_Date = $data['KB2_TRAN_DAT'];
                $answer[$key] -> transDateFlag = 0;
                $answer[$key] -> Transaction_Type = $data['KB2_TRAN_TYPE'];
                $answer[$key] -> transTypeFlag = 0; 
                $answer[$key] -> Umpedict_Number = $data['KB2_UNPREDICT_NUM'];
                $answer[$key] -> umpNumFlag = 0;
                $answer[$key] -> Issuing_App_Data_Length = $data['KB2_ISS_APPL_DATA_LGTH'];
                $answer[$key] -> appDataLenFlag = 0;
                $answer[$key] -> Issuing_App_Data = $data['KB2_ISS_APPL_DATA'];
                $answer[$key] -> appDataFlag = 0;
                $answer[$key] -> TVR = $data['KB2_TVR'];
                $answer[$key] -> tvrFlag = 0;
                $answer[$key] -> AIP = $data['KB2_AIP'];
                $answer[$key] -> aipFlag = 0;
            }
        }

        foreach($array as $key => $data){
            $bitMapFlag = 0; $userFOFlag = 0; $arqcFlag = 0; $amtAuthFlag = 0;
            $amtOtherFlag = 0; $atcFlag = 0; $termConFlag = 0; $termCurrFlag = 0;
            $transDateFlag = 0; $transTypeFlag = 0; $umpNumFlag = 0; $tvrFlag = 0;
            $appDataLenFlag = 0; $appDataFlag = 0; $cryptoFlag = 0; $aipFlag = 0;
            //Conversión a binario del campo Crypto_Data, se rellenan los espacios con ceros binarios
            $cryptoDataBinary = str_pad(base_convert($data['KB2_CRYPTO_INFO_DATA'],16,2),8,'0',STR_PAD_LEFT);
            //Conversión del dato binario a un arreglo para su posterior validación
            $arrayCrypto = str_split($cryptoDataBinary, 1);

            $tvrDataBinary = str_pad(base_convert($data['KB2_TVR'],16,2),40,'0',STR_PAD_LEFT);
            $arrayTvr = str_split($tvrDataBinary, 1);

            $aipDataBinary = str_pad(base_convert($data['KB2_AIP'],16,2),16,'0',STR_PAD_LEFT);
            $arrayAip = str_split($aipDataBinary, 1);

            if(strlen($data['KB2_BIT_MAP']) == 4){
                $bitMapFlag = 1;

                if(strlen($data['KB2_USR_FLD1']) == 4){ $userFOFlag = 1; };
                if(strlen($data['KB2_ARQC']) == 16) { $arqcFlag = 1; }
                if(strlen($data['KB2_AMT_AUTH']) == 12) { $amtAuthFlag = 1; }
                if(strlen($data['KB2_AMT_OTHER']) == 12) { $amtOtherFlag = 1; }
                if(strlen($data['KB2_ATC']) == 4) { $atcFlag = 1; }
                if(strlen($data['KB2_TERM_CTRY_CDE']) == 3) { $termConFlag = 1; }
                if(strlen($data['KB2_TRAN_CRNCY_CDE']) == 3) { $termCurrFlag = 1; }
                if(strlen($data['KB2_TRAN_DAT']) == 6) { $transDateFlag = 1; }
                if(strlen($data['KB2_TRAN_TYPE']) == 2) { $transTypeFlag = 1; }
                if(strlen($data['KB2_UNPREDICT_NUM']) == 8) { $umpNumFlag = 1; }
                if(strlen($data['KB2_ISS_APPL_DATA_LGTH']) == 4) { $appDataLenFlag = 1; }
                if(strlen($data['KB2_ISS_APPL_DATA']) == 64) { $appDataFlag = 1; }

                //Comparación con cada uno de los dígitos del campo.
                if(strlen($data['KB2_CRYPTO_INFO_DATA']) == 2){
                    if($arrayCrypto[0].$arrayCrypto[1] < $fisrtCombinationNumber){
                        if($arrayCrypto[4] == 0 || $arrayCrypto[4] == 1){
                            if($arrayCrypto[5] !== 0){
                                if(($arrayCrypto[6].$arrayCrypto[7] < $fisrtCombinationNumber)){
                                    $cryptoFlag = 1;
                                }
                            }
                        } 
                    }
                }else { $cryptoFlag = 0;};

                //Validación del campo TVR
                if(strlen($data['KB2_TVR']) == 10){
                    $firstByteFlag = false;
                    $secondByteFlag = false;
                    $thirdBtyeFlag = false;
                    $fourthByteFlag = false;
                    $fifthByteFlag = false;
                    //Validación del primer byte (8 primeras posiciones) del campo TVR
                    //Solo se valida de la posición 0 a 4 (0-7)
                    for($i = 0; $i < 5; $i++){
                        if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                            $firstByteFlag = true;
                        }else{ $firstByteFlag = false; $i = 6;}
                    }
                    //Validación del segundo byte (8 segundas posiciones)
                    //Sólo se valida de la posición 0 a 4 (8-15)
                    if($firstByteFlag){
                        for($i = 8; $i < 13; $i++){
                            if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                                $secondByteFlag = true;
                            }else { $secondByteFlag = false; $i = 14;}
                        }
                    }
                    //Validación del tercer byte (8 terceras posiciones)
                    //Sólo se valida de la posición 0 a 5 (16-23)
                    if($secondByteFlag){
                        for($i = 16; $i < 22; $i++){
                            if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                                $thirdBtyeFlag = true;
                            }else { $thirdBtyeFlag = false; $i = 23;}
                        }
                    }
                    //Validación del cuarto byte (8 cuartas posiciones)
                    //Solo se valida de la posición 0 a 4 (24-31)
                    if($thirdBtyeFlag){
                        for($i = 24; $i < 29; $i++){
                            if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                                $fourthByteFlag = true;
                            }else { $fourthByteFlag = false; $i = 30;}
                        }
                    }
                    //Validación del quinto byte (8 quintas posiciones)
                    //Solo se valida de la posición 0 a 3 (32-39)
                    if($fourthByteFlag){
                        for($i = 32; $i < 35; $i++){
                            if($arrayTvr[$i] == 0 || $arrayTvr[$i] == 1){
                                $fifthByteFlag = true;
                            }else { $fifthByteFlag = false; $i = 36;}
                        }
                    }
                    if($fifthByteFlag){ $tvrFlag = 1;} 
                }

                //Validación del campo AIP
                if(strlen($data['KB2_AIP']) == 4){
                    //Validación del primer byte (8 primeras posiciones)
                    //Solo se valida de la posición 0 a 5
                    $byteFlag = false;
                    for($i = 0; $i < 6; $i++){
                        if($arrayAip[$i] == 0 || $arrayAip[$i] == 1){
                            $byteFlag = true;
                        }else { $byteFlag = false;  $i = 7;}
                    }
                    if($byteFlag){ $aipFlag = 1; }
                }

                $answer[$key] = new stdClass();
                $answer[$key] -> Bit_Map = $data['KB2_BIT_MAP'];
                $answer[$key] -> bitMapFlag = $bitMapFlag;
                $answer[$key] -> User_Field_One = $data['KB2_USR_FLD1'];
                $answer[$key] -> userFOFlag = $userFOFlag;
                $answer[$key] -> Crypto_Data = $data['KB2_CRYPTO_INFO_DATA'];
                $answer[$key] -> cryptoFlag = $cryptoFlag;
                $answer[$key] -> ARQC = $data['KB2_ARQC'];
                $answer[$key] -> arqcFlag = $arqcFlag;
                $answer[$key] -> AMT_Auth = $data['KB2_AMT_AUTH'];
                $answer[$key] -> amtAuthFlag = $amtAuthFlag;
                $answer[$key] -> AMT_Other = $data['KB2_AMT_OTHER'];
                $answer[$key] -> amtOtherFlag = $amtOtherFlag;
                $answer[$key] -> ATC = $data['KB2_ATC'];
                $answer[$key] -> atcFlag = $atcFlag;
                $answer[$key] -> Terminal_Country_Code = $data['KB2_TERM_CTRY_CDE'];
                $answer[$key] -> termConFlag = $termConFlag;
                $answer[$key] -> Terminal_Currency_Code = $data['KB2_TRAN_CRNCY_CDE'];
                $answer[$key] -> termCurrFlag = $termCurrFlag;
                $answer[$key] -> Transaction_Date = $data['KB2_TRAN_DAT'];
                $answer[$key] -> transDateFlag = $transDateFlag;
                $answer[$key] -> Transaction_Type = $data['KB2_TRAN_TYPE'];
                $answer[$key] -> transTypeFlag = $transTypeFlag; 
                $answer[$key] -> Umpedict_Number = $data['KB2_UNPREDICT_NUM'];
                $answer[$key] -> umpNumFlag = $umpNumFlag;
                $answer[$key] -> Issuing_App_Data_Length = $data['KB2_ISS_APPL_DATA_LGTH'];
                $answer[$key] -> appDataLenFlag = $appDataLenFlag;
                $answer[$key] -> Issuing_App_Data = $data['KB2_ISS_APPL_DATA'];
                $answer[$key] -> appDataFlag = $appDataFlag;
                $answer[$key] -> TVR = $data['KB2_TVR'];
                $answer[$key] -> tvrFlag = $tvrFlag;
                $answer[$key] -> AIP = $data['KB2_AIP'];
                $answer[$key] -> aipFlag = $aipFlag;
            }
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        $arrayJSONOrdered = array_values($arrayJSON);
        return $arrayJSONOrdered;
    }

    public function getDataTableFilter(Request $request){
        $values = array();
        $label = ['KB2_BIT_MAP', 'KB2_USR_FLD1', 'KB2_ARQC', 'KB2_AMT_AUTH', 'KB2_AMT_OTHER', 'KB2_ATC',
        'KB2_TERM_CTRY_CDE', 'KB2_TRAN_CRNCY_CDE', 'KB2_TRAN_DAT', 'KB2_TRAN_TYPE', 'KB2_UNPREDICT_NUM',
        'KB2_ISS_APPL_DATA_LGTH', 'KB2_ISS_APPL_DATA', 'KB2_TVR', 'KB2_AIP'];

        $values[0] = $request -> Bit_Map;
        $values[1] = $request -> User_Field_One;
        $values[2] = $request -> ARQC;
        $values[3] = $request -> AMT_Auth;
        $values[4] = $request -> AMT_Other;
        $values[5] = $request -> ATC;
        $values[6] = $request -> Terminal_Country_Code;
        $values[7] = $request -> Terminal_Currency_Code;
        $values[8] = $request -> Transaction_Date;
        $values[9] = $request -> Transaction_Type;
        $values[10] = $request -> Umpedict_Number;
        $values[11] = $request -> Issuing_App_Data_Length;
        $values[12] = $request -> Issuing_App_Data;
        $values[13] = $request -> TVR;
        $values[14] = $request -> AIP;

        $answer = array();

        for($key = 0; $key < 15; $key++){
            if($values[$key] == "NonValue"){
                unset($values[$key]);
                unset($label[$key]);
            }
        };
        $filteredValues = array_values($values);
        $filteredLabels = array_values($label);

        for($key = 0; $key < sizeof($filteredValues); $key++){
            $response = DB::select("select FIID_TARJ,FIID_COMER,NOMBRE_DE_TERMINAL,CODIGO_RESPUESTA,R,
            NUM_SEC,KQ2_ID_MEDIO_ACCESO,ENTRY_MODE,MONTO1 from test where ".$filteredLabels[$key]." = '".$filteredValues[$key]."'");
            $array = json_decode(json_encode($response), true); 
        }

        foreach($array as $key => $data){
            if($data['CODIGO_RESPUESTA'] > 010){
                $answer[$key] = new stdClass();
                $answer[$key] -> Fiid_Card = $data['FIID_TARJ'];
                $answer[$key] -> Fiid_Comerce = $data['FIID_COMER'];
                $answer[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
                $answer[$key] -> Code_Response = $data['CODIGO_RESPUESTA'];
                $answer[$key] -> R = $data['R'];
                $answer[$key] -> Number_Sec = $data['NUM_SEC'];
                $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                $answer[$key] -> entryMode = $data['ENTRY_MODE'];
                $answer[$key] -> amount = number_format($data['MONTO1'], 2, '.');
            }
        }
        foreach($array as $key => $data){
            if($data['CODIGO_RESPUESTA'] < 010){
                $answer[$key] = new stdClass();
                $answer[$key] -> Fiid_Card = $data['FIID_TARJ'];
                $answer[$key] -> Fiid_Comerce = $data['FIID_COMER'];
                $answer[$key] -> Terminal_Name = $data['NOMBRE_DE_TERMINAL'];
                $answer[$key] -> Code_Response = $data['CODIGO_RESPUESTA'];
                $answer[$key] -> R = $data['R'];
                $answer[$key] -> Number_Sec = $data['NUM_SEC'];
                $answer[$key] -> ID_Access_Mode = $data['KQ2_ID_MEDIO_ACCESO'];
                $answer[$key] -> entryMode = $data['ENTRY_MODE'];
                $answer[$key] -> amount = number_format($data['MONTO1'], 2, '.');
            }
        }
        $arrayJSON = json_decode(json_encode($answer), true);
        $arrayJSONOrdened = array_values($arrayJSON);
        return $arrayJSONOrdened;
    }
}
