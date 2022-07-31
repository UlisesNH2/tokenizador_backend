<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class BreakerController extends Controller
{
    public function getBreakes(Request $request){
        $message =  $request -> message;
        $positions = array();
        $initPos = 32; $finalPos = 47;
        //Variables de cada uno de los Campos
        $secondaryBitmap = ''; $secondaryBMValue = ''; $primAcNumber = ''; $processingCode = '';
        $amount = ''; $dateAndTime = ''; $sysTAN = ''; $localTransactionTime = ''; $localTransactionDate = '';
        $isoWord = $message[0].$message[1].$message[2]; //Extraer la palabra ISO
        $response = new stdClass();
        if($isoWord === "ISO"){ //Validación si la palabra 'ISO' viene al principio del mensaje
            //Extraer el header TODO: VALIDACION PENDIENTE
            $header = $this -> getChain($message, 3, 11); 
            //Extraer el type TODO: VALIDACION PENDIENTE
            $type = $this -> getChain($message, 12, 15);
            //Extraer el bitmap principal
            $mainBitmap = $this -> getChain($message, 16, 31);
            //Conversión a binario del bitmap principal
            $binaryBitmap = $this -> getBinary($mainBitmap, 0, 16);

            //Se ingresa al response los siguienetes datos: header, tipo, bitmap principal. (Datos principales)
            $response -> message = $message;
            $response -> header = $isoWord.$header;
            $response -> type = $type;

            //Identificación de los campos habilidatos de acuerdo al bitmap principal
            for($i = 0; $i < strlen($binaryBitmap); $i++){
                if($binaryBitmap[$i] === '1'){
                    array_push($positions, $i+1); //Se ingresan las posiciones habilidatas a un arreglo
                }
            }
            for($i = 0; $i < count($positions); $i++){
                switch($positions[$i]){
                    case 1:{ //Secondary bitmap
                        $secondaryBitmap = $this -> getChain($message, 32, 47); //Logitud: 16
                        //Conversión a binario del secondary bitmap
                        $secondaryBMValue = $this -> getBinary($secondaryBitmap, 0, 16);
                        $response -> bitmap = $mainBitmap.$secondaryBitmap;
                        $response -> secondaryBitmapValue = $secondaryBMValue;
                        break;
                    }
                    case 2:{//Primary Account Number
                        $initPos = $finalPos+1; $finalPos += 18;  
                        $primAcNumber = $this -> getChain($message, $initPos, $finalPos); //Longitud: 19
                        $response -> primaryActNum = $primAcNumber;
                        break;
                    }
                    case 3: { //Processing Code
                        $initPos = $finalPos+1; $finalPos += 6;
                        $processingCode = $this -> getChain($message, $initPos, $finalPos); //Longitud: 6
                        $response -> processingCode = $processingCode;
                        break;
                    }
                    case 4:{ //Amount
                        $initPos = $finalPos+1; $finalPos += 12;
                        $amount = $this -> getChain($message, $initPos, $finalPos); //Longitud: 12
                        $response -> amount = $amount;
                        break;
                    }
                    case 5: { //Settlement amount
                        $initPos = $finalPos+1; $finalPos += 12;
                        $setAmount = $this -> getChain($message, $initPos, $finalPos); //Longitud: 12
                        $response -> setAmount = $setAmount;
                        break;
                    }
                    case 6: { //Cardholder Billing Amount
                        $initPos = $finalPos+1; $finalPos += 12;
                        $chdAmount = $this -> getChain($message, $initPos, $finalPos); //Longitud 12
                        $response -> chdAmount = $chdAmount;
                        break;
                    }
                    case 7: { //Date and Time
                        $initPos = $finalPos+1; $finalPos += 10;
                        $dateAndTime = $this -> getChain($message, $initPos, $finalPos); //Longitud: 10
                        $response -> dateTime = $dateAndTime;
                        break;
                    }
                    case 8: { //Cardholder Billing Fee Amount
                        $initPos = $finalPos+1; $finalPos += 8;
                        $chFeeAmount = $this -> getChain($message, $initPos, $finalPos); //Longitud 8
                        $response -> chFeeAmount = $chFeeAmount;
                        break;
                    }
                    case 9:{ //Settlement Conversion Rate 
                        $initPos = $finalPos+1; $finalPos += 8;
                        $setConRate = $this -> getChain($message, $initPos, $finalPos); //Longitud 8
                        $response -> setConvRate = $setConRate;
                        break;
                    }
                    case 10:{ //Cardholder Billing Conversion Rate
                        $initPos = $finalPos+1; $finalPos += 8;
                        $chBillConv = $this -> getChain($message, $initPos, $finalPos); //Longitud 9
                        $response -> chBillConv = $chBillConv;
                        break;
                    }
                    case 11: { //System Trace Audit Number
                        $initPos = $finalPos+1; $finalPos += 6;
                        $sysTAN = $this -> getChain($message, $initPos, $finalPos); //Longitud: 6
                        $response -> systemsTAN = $sysTAN;
                        break;
                    }
                    case 12: { //Local Transaction Time
                        $initPos = $finalPos+1; $finalPos += 6;
                        $localTransactionTime = $this -> getChain($message, $initPos, $finalPos); //Longitud: 6
                        $response -> localTT = $localTransactionTime;
                        break;
                    }
                    case 13: { //Local Tansaction Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $localTransactionDate = $this -> getChain($message, $initPos, $finalPos); //Longitud 6
                        $response -> localTD = $localTransactionDate;
                        break;
                    }
                    case 14:{ //Expiration Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $expDate = $this -> getChain($message, $initPos, $finalPos); //Longitud 4
                        $response -> expDate = $expDate;
                        break;
                    }
                    case 15: { //Settlement Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $setDate = $this -> getChain($message, $initPos, $finalPos); //Long 4
                        $response -> setDate = $setDate;
                        break;
                    }
                    case 16: { //Conversion Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $convDate = $this -> getChain($message, $initPos, $finalPos); //Long 4
                        $response -> convData = $convDate;
                        break; 
                    }
                    case 17:{ //Capture Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $capDate = $this -> getChain($message, $initPos, $finalPos); //Long 4
                        $response -> capData = $capDate;
                        break;
                    }
                    case 18:{ //Merchant Type
                        $initPos = $finalPos+1; $finalPos += 4;
                        $merchantType = $this -> getChain($message, $initPos, $finalPos); //Long 4
                        $response -> merchantType = $merchantType;
                        break;
                    }
                    case 19: { //Aqcuiring Institution Country Code
                        $initPos = $finalPos+1; $finalPos += 3;
                        $countryCode = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $response -> countryCode = $countryCode;
                        break;
                    }
                    case 20: { //Country Code Primary Account Number Ext
                        $initPos = $finalPos+1; $finalPos += 3;
                        $countryCodeNumbExt = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $response -> countryCodeExt = $countryCodeNumbExt;
                        break;
                    }
                    case 21: { //Forwarding Institution Country Code
                        $initPos = $finalPos+1; $finalPos += 3;
                        $forwardCC = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $response -> forwardCC = $forwardCC;
                        break;
                    }
                    case 22:{ //POS Entry Mode
                        $initPos = $finalPos+1; $finalPos += 3;
                        $posEmode = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $response -> posEntryMode = $posEmode;
                        break;
                    }
                    case 23: { //Card Sequence Number 
                        $initPos = $finalPos+1; $finalPos += 3;
                        $cardSeqNum = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $response -> cardSeqNum = $cardSeqNum;
                        break;
                    }
                    case 24: { //Network International Identifier
                        $initPos = $finalPos+1; $finalPos += 3;
                        $networkII = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $response -> netwotkII = $networkII;
                        break;
                    }
                    case 25: { //POS Condition Code 
                        $initPos = $finalPos+1; $finalPos += 2;
                        $posConditionCode = $this -> getChain($message, $initPos, $finalPos); //Long 2
                        $response -> posCondCode = $posConditionCode;
                        break;
                    }
                    case 26: { // POS PIN Code
                        $initPos = $finalPos+1; $finalPos += 2;
                        $pinCode = $this -> getChain($message, $initPos, $finalPos); //Long 2
                        $response -> pinCode = $pinCode;
                        break;
                    }
                    case 27: { //Auth Identification Response Length
                        $initPos = $finalPos+1; $finalPos += 1;
                        $authIdent = $this -> getChain($message, $initPos, $finalPos); //Long 1
                        $response -> authIdent = $authIdent;
                        break;
                    }
                    case 28: { //Transaction Fee Amount
                        $initPos = $finalPos+1; $finalPos += 8;
                        $tranFeeAm = $this -> getChain($message, $initPos, $finalPos); //Long 8
                        $response -> tranFeeAm = $tranFeeAm;
                        break;
                    }
                    case 29: { //Settlement Fee Amount
                        $initPos = $finalPos+1; $finalPos += 8;
                        $setFeeAm = $this -> getChain($message, $initPos, $finalPos); //Long 8
                        $response -> setFeeAm = $setFeeAm;
                        break;
                    }
                    case 30: { //Transaction Proccesing Fee Amount
                        $initPos = $finalPos+1; $finalPos += 8;
                        $transFeeAmount = $this -> getChain($message, $initPos, $finalPos); //Long 8
                        $response -> transFeeAmount = $transFeeAmount;
                        break;
                    }
                    case 31: { //Settlement Processing Fee Amonut
                        $initPos = $finalPos+1; $finalPos += 8;
                        $setProFeeAmount = $this -> getChain($message, $initPos, $finalPos); //Long 8
                        $response -> setProFeeAmount = $setProFeeAmount;
                        break;
                    }
                    case 32:{ //Aqcuiring Institution ID Code
                        $initPos = $finalPos+1; $finalPos += 2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        if(ltrim($len, '0') <= 11){
                            $initPos = $finalPos+1; $finalPos += ltrim($len, '0');
                            $aqrCode = $this -> getChain($message, $initPos, $finalPos); //Long 11
                            $response -> arqCode = str_pad($aqrCode, 11, '0', STR_PAD_LEFT);
                        }else{
                            $response -> arqCode = '------';
                        }
                        break;
                    }
                    case 33: { //Fordwardig Institution Identification Code
                        $initPos = $finalPos+1; $finalPos += 11;
                        $fordCode = $this -> getChain($message, $initPos, $finalPos); //Long 11
                        $response -> fordwardCode = $fordCode;
                        break;
                    }
                    case 34: { //Extending Primary Account Number
                        $initPos = $finalPos+1; $finalPos += 28;
                        $extAccNum = $this -> getChain($message, $initPos, $finalPos); //Long 28
                        $response -> extAccNumber = $extAccNum;
                        break;
                    }
                    case 35: { //Track 2 Data
                        $initPos = $finalPos+1; $finalPos += 2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        if(intval($len) <= 37){
                            $initPos = $finalPos+1; $finalPos += intval($len);
                            $track2Data = $this -> getChain($message, $initPos, $finalPos); //Long 37
                            $response -> track2Data = $track2Data;
                        }else{
                            $response -> track2Data = '------';
                        }
                        break;
                    }
                    case 36: { 
                        
                    }
                    case 37: { //Retrivel Reference Number
                        $initPos = $finalPos+1; $finalPos += 12;
                        $retRefNum = $this -> getChain($message, $initPos, $finalPos); //Long 12
                        $response -> retRefNum = $retRefNum;
                        break;
                    }
                    case 38: { //Autorization Identification Response
                        $initPos = $finalPos+1; $finalPos += 6;
                        $authIDResp = $this -> getChain($message, $initPos, $finalPos); //Long 6
                        $response -> authIDResp = $authIDResp;
                        break;
                    }
                    case 39: { //Response Code
                        $initPos = $finalPos+1; $finalPos += 2;
                        $respCode = $this -> getChain($message, $initPos, $finalPos); //Long 2
                        $response -> respCode = $respCode;
                        break;
                    }
                    case 40: {

                        break;
                    }
                    case 41: { //Card Aceptor Terminal ID
                        $initPos = $finalPos+1; $finalPos += 16;
                        $cardAcTermID = $this -> getChain($message, $initPos, $finalPos); //Long 16
                        $response -> cardAcTerm = $cardAcTermID;
                        /*
                        if($cardAcTermID[9] !== ' '){
                            $response -> cardAcTermID = $cardAcTermID;
                        }else{
                            for($j = 9; $j < 16; $j++){
                                $cardAcTermID[$j] = ' ';
                            }
                            $response -> cardAcTermID = $cardAcTermID.'-';
                            $initPos = $finalPos-7;
                            $finalPos -= 7;
                        }
                        */
                        break;
                    }
                    case 42: { //Card Acceptor ID Code
                        $initPos = $finalPos+1; $finalPos += 15;
                        $cardAcIDCode = $this -> getChain($message, $initPos, $finalPos); //Long 16
                        $response -> cardAcIDCode = $cardAcIDCode;
                        break;
                    }
                    case 43: { //Card Aceptor Name / Location
                        $initPos = $finalPos+1; $finalPos += 40;
                        $cardAcName = $this -> getChain($message, $initPos, $finalPos); //Long 40
                        $response -> cardAcName = $cardAcName;
                        break;
                    }
                    case 44:{
                        break;
                    }
                    case 45: {
                        break;
                    }
                    case 46: {
                        break;
                    }
                    case 47: {
                        break;
                    }
                    case 48: { //Retailer Data
                        //Primeras tres posiciones indica la longitud del campo
                        $initPos = $finalPos+1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        if(ltrim($len, '0') <= 30){
                            $initPos = $finalPos+1; $finalPos += intval(ltrim($len, '0'));
                            $retailerData = $this -> getChain($message, $initPos, $finalPos); //Long 30
                            $response -> retailerData = $retailerData;
                        }else{
                            $response -> retailerData = '-------';
                        }
                        break;
                    }
                    case 49: { //Transaction Currency Code
                        $flag = 0;
                        $initPos = $finalPos+1; $finalPos += 3;
                        $currCode = $this -> getChain($message, $initPos, $finalPos);
                        $code = DB::select('select CURRENCY_CODE from catalog_currency_code');
                        $array = json_decode(json_encode($code), true);
                        foreach ($array as $key => $data) {
                            if($currCode === $data['CURRENCY_CODE']){ 
                                $flag = 1; 
                                //$code = [];
                            }
                        }
                        if($flag == 1){
                            //$initPos = $finalPos+1; $finalPos += 3;
                            $currCode = $this -> getChain($message, $initPos, $finalPos);
                            $response -> currCode = $currCode;
                        }else{
                            //$initPos = $finalPos+1; $finalPos += 3;
                            $response -> currCode = '-------';
                        }
                        break;
                    }
                    case 50: {
                        
                        break;
                    }
                    case 52: {  //PIN
                        $initPos = $finalPos+1; $finalPos += 16;
                        $pin = $this -> getChain($message, $initPos, $finalPos);
                        $response -> pin = $pin;
                        break;
                    }
                    //TODO 51 - 59
                    case 60: { //Terminal Data
                        $initPos = $finalPos+1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        if(ltrim($len, '0') <= 19){
                            $initPos = $finalPos+1; $finalPos += ltrim($len, '0');
                            $termData = $this -> getChain($message, $initPos, $finalPos); //Long 19
                            $response -> termData = $termData;
                        }else{
                            $response -> termData = '-------';
                        }
                        break;
                    }
                    case 61: { //Response Code Data
                        $initPos = $finalPos+1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        if(ltrim($len, '0') <= 22){
                            $initPos = $finalPos+1; $finalPos += ltrim($len, '0');
                            $respCodeData = $this -> getChain($message, $initPos, $finalPos); //Long 22
                            $response -> respCodeData = $respCodeData;
                        }else{
                            $response -> respCodeData = '-------';
                        }
                        break;
                    }
                    case 62: { //Postal Code
                        $initPos = $finalPos+1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        if(ltrim($len, '0') <= 13){
                            if(ltrim($len, '0') == 10){
                                $initPos = $finalPos+1; $finalPos += ltrim($len, '0');
                                $postalCode = $this -> getChain($message, $initPos, $finalPos);
                                $response -> postalCode = $postalCode;
                            }else{
                                $initPos = $finalPos+1; $finalPos += 10;
                                $postalCode = $this -> getChain($message, $initPos, $finalPos);
                                $response -> postalCode = $postalCode.'-';
                            }
                        }
                        break;
                    }
                    case 63: { //Aditional Data
                        $initPos = $finalPos+1; $finalPos += 3;
                        //Las primeras tres posiciones son la longitud del campo: TODO: validación
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $additionalData = $this -> getChain($message, $initPos+3, $finalPos + intval($len));
                        $response -> additionalData = $additionalData;
                        //Obtención del header para la lectura y desglose de los tokens
                        $initPos = $finalPos+1; $finalPos += 12; 
                        $headerAllTokens = $this -> getChain($message, $initPos, $finalPos);
                        $response -> addDataHeader = $headerAllTokens;
                        //Validación del header
                        if($headerAllTokens[0] === '&'){ //Eye - Catcher
                            if($headerAllTokens[1] === ' '){ //User-filed
                                //Obtener el número de tokens que hay en el mensaje
                                //ltrim() -> función para quitar los caracteres deseados de la izquierda
                                $numberOfTokens = ltrim($this -> getChain($headerAllTokens, 2, 6), '0')-1;
                                $initPos = $finalPos+1; $finalPos += 10; //Tamaño del token header
                                for($x = 0; $x < $numberOfTokens; $x++){
                                    $tokenHeader = ''; $idToken = ''; $lenToken = '';
                                    $tokenHeader = $this -> getChain($message, $initPos, $finalPos);
                                    //Nombres para el objeto
                                    $idToken = $this -> getChain($tokenHeader, 0, 3);
                                    $idTokenString = $this -> getChain($idToken, 2, 3);
                                    $lenString = $this -> getChain($idToken, 2, 3).'-Longitud';
                                    $valueTokenString = $this -> getChain($idToken, 2, 3).'-Contenido';
                                    //Valores para el objeto
                                    $lenToken = $this -> getChain($tokenHeader, 4, 8);
                                    $value = $this -> getChain($message, $finalPos+1, $finalPos+intval(ltrim($lenToken, '0')));
                                    //Creación del objeto
                                    $response -> $idTokenString = $idToken;
                                    $response ->  $lenString = $lenToken;
                                    $response -> $valueTokenString = $value;
                                    //Aumento en las posiciones respectivas
                                    if(strpos($value, '!')){
                                        for($y = 1; $y < strlen($value); $y++){
                                            if($value[$y] === '!'){
                                                $dif = strlen($value) - $y;
                                                $firstPart = substr($message, 0, $finalPos + $y);
                                                for($w = 0; $w < $dif; $w++){
                                                    $firstPart .= 'X';
                                                }
                                                $secondPart = substr($message, $finalPos + $y);
                                                $message = $firstPart.$secondPart;
                                                $x--;
                                            }
                                        }
                                    }else{
                                        $initPos = $finalPos + intval(ltrim($lenToken, '0'))+1;
                                        $finalPos += 10 + intval(ltrim($lenToken, '0'));
                                    }
                                }
                            }else{
                                //Obtener el número de tokens que hay en el mensaje
                                //ltrim() -> función para quitar los caracteres deseados de la izquierda
                                $numberOfTokens = ltrim($this -> getChain($headerAllTokens, 2, 5), '0')-1;
                                $initPos = $finalPos; $finalPos += 10; //Tamaño del token header
                                for($x = 0; $x < $numberOfTokens; $x++){
                                    $tokenHeader = ''; $idToken = ''; $lenToken = '';
                                    $tokenHeader = $this -> getChain($message, $initPos, $finalPos);
                                    //Nombres para el objeto
                                    $idToken = $this -> getChain($tokenHeader, 0, 3);
                                    $idTokenString = $this -> getChain($idToken, 2, 3);
                                    $lenString = $this -> getChain($idToken, 2, 3).'-Longitud';
                                    $valueTokenString = $this -> getChain($idToken, 2, 3).'-Contenido';
                                    //Valores para el objeto
                                    $lenToken = $this -> getChain($tokenHeader, 4, 8);
                                    $value = $this -> getChain($message, $finalPos+1, $finalPos+intval(ltrim($lenToken, '0')));
                                    //Creación del objeto
                                    $response -> $idTokenString = $idToken;
                                    $response ->  $lenString = $lenToken;
                                    $response -> $valueTokenString = $value;
                                    //Aumento en las posiciones respectivas
                                    if(strpos($value, '!')){
                                        for($y = 1; $y < strlen($value); $y++){
                                            if($value[$y] === '!'){
                                                $dif = strlen($value) - $y;
                                                $firstPart = substr($message, 0, $finalPos + $y);
                                                for($w = 0; $w < $dif; $w++){
                                                    $firstPart .= 'X';
                                                }
                                                $secondPart = substr($message, $finalPos + $y);
                                                $message = $firstPart.$secondPart;
                                                $x--;
                                            }
                                        }
                                    }else{
                                        $initPos = $finalPos + intval(ltrim($lenToken, '0'))+1;
                                        $finalPos += 10 + intval(ltrim($lenToken, '0'));
                                    }
                                }
                            }
                        }
                    }
                }
            } 
        }
        $response -> positions = $positions;
        $responseJSON = json_decode(json_encode($response), true);
        $res = array();
        $res[0] = new stdClass();
        $res[0] = $responseJSON;
        return json_decode(json_encode($res), true);
        return $responseJSON;
    }

    //Función para obtener el header del mensaje.
    function getChain($message, $initPos, $finalPos){
        $chain = '';
        for($i = $initPos; $i < $finalPos+1; $i++){
            $chain .= $message[$i];
        }
        return $chain;
    }

    function getBinary($bitmap, $initPos, $finalPos){
        $binaryValues = array();
        $positions = '';
        for($i = $initPos; $i < $finalPos; $i++){
            array_push($binaryValues, str_pad(base_convert($bitmap[$i], 16, 2), 4, '0', STR_PAD_LEFT));
        }
        for($i = 0; $i < count($binaryValues); $i++){
            $positions .= $binaryValues[$i];
        }
        return $positions;
    }

    public function getCatalog(Request $request){
        $values = $request -> positions;
        $catalog = array();
        $response = array();

        for($i = 0; $i < count($values); $i++){
            $catalog = array_merge($catalog, DB::select("select * from catalog_message where ID = ?", [$values[$i]]));
        }
        $cat = json_decode(json_encode($catalog), true);

        foreach($cat as $key => $data){
            $response[$key] = new stdClass();
            $response[$key] -> id = $data['ID'];
            $response[$key] -> field = $data['CAMPO'];
            $response[$key] -> name = $data['NOMBRE'];
            $response[$key] -> type = $data['TIPO_DATO'];
        }
        $arrayJSON = json_decode(json_encode($response), true);
        return $arrayJSON;
    }
}
