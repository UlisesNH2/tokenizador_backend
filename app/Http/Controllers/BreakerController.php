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
        $response = array();
        $initPos = 32; $finalPos = 47;
        //Variables de cada uno de los Campos
        $secondaryBitmap = ''; $secondaryBMValue = '';
        $isoWord = $message[0].$message[1].$message[2]; //Extraer la palabra ISO
        
        if($isoWord === "ISO"){ //Validación si la palabra 'ISO' viene al principio del mensaje
            //Extraer el header TODO: VALIDACION PENDIENTE
            $header = $this -> getChain($message, 3, 11); 
            //Extraer el type TODO: VALIDACION PENDIENTE
            $typeMess = $this -> getChain($message, 12, 15);
            //Extraer el bitmap principal
            $mainBitmap = $this -> getChain($message, 16, 31);
            //Conversión a binario del bitmap principal
            $binaryBitmap = $this -> getBinary($mainBitmap, 0, 16);
            //Conversión a binario del secondary bitmap
            $secondaryBitmap = $this -> getChain($message, 32, 47); //Logitud: 16
            $secondaryBMValue = $this -> getBinary($secondaryBitmap, 0, 16);
            //Se ingresa al response los siguienetes datos: header, tipo, bitmap principal. (Datos principales)

            $counter = 0; //Indicador/controlador de la cantidad de objetos que hay en el arreglo final
            $response[$counter] = new stdClass();
            $response[$counter] -> message = $message;
            $response[$counter] -> header = $isoWord.$header;
            $response[$counter] -> typeMess = $typeMess;
            $response[$counter] -> bitmap = $mainBitmap.$secondaryBitmap;

            //Identificación de los campos habilidatos de acuerdo al bitmap principal
            for($i = 0; $i < strlen($binaryBitmap); $i++){
                if($binaryBitmap[$i] === '1'){
                    array_push($positions, $i+1); //Se ingresan las posiciones habilidatas a un arreglo
                }
            }
            
            //Identificación de los campos habilitados en base al bitmap secundario
            for($i = 0; $i < strlen($secondaryBMValue); $i++){
                if($secondaryBMValue[$i] === '1'){
                    array_push($positions, $i+65); //Se ingresan al mismo arreglo las posiciones habilitadas para el arreglo
                }
            }
            //Se obtiene el catalogo de acuerdo a las posiciones obtenidas en el bitmap
            $catalog = $this -> getCatalog($positions);
            //return $positions;
            $number = 'number'; $field = 'field'; $name = 'name'; $type = 'type'; $value = 'value'; $id = 3;
            //return $positions;
            for($i = 0; $i < count($positions); $i++){
                switch($positions[$i]){
                    case 1:{ //Secondary bitmap
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $secondaryBMValue;
                        break;
                    }
                    case 2:{//Primary Account Number
                        $initPos = $finalPos+1; $finalPos += 18;  
                        $primAcNumber = $this -> getChain($message, $initPos, $finalPos); //Longitud: 19
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $primAcNumber;
                        break;
                    }
                    case 3: { //Processing Code
                        $initPos = $finalPos+1; $finalPos += 6;
                        $processingCode = $this -> getChain($message, $initPos, $finalPos); //Longitud: 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $processingCode;
                        break;
                    }
                    case 4:{ //Amount
                        $initPos = $finalPos+1; $finalPos += 12;
                        $amount = $this -> getChain($message, $initPos, $finalPos); //Longitud: 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $amount;
                        break;
                    }
                    case 5: { //Settlement amount
                        $initPos = $finalPos+1; $finalPos += 12;
                        $setAmount = $this -> getChain($message, $initPos, $finalPos); //Longitud: 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $setAmount; 
                        break;
                    }
                    case 6: { //Cardholder Billing Amount
                        $initPos = $finalPos+1; $finalPos += 12;
                        $chdAmount = $this -> getChain($message, $initPos, $finalPos); //Longitud 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $chdAmount;
                        break;
                    }
                    case 7: { //Date and Time
                        $initPos = $finalPos+1; $finalPos += 10;
                        $dateAndTime = $this -> getChain($message, $initPos, $finalPos); //Longitud: 10
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $dateAndTime;
                        break;
                    }
                    case 8: { //Cardholder Billing Fee Amount
                        $initPos = $finalPos+1; $finalPos += 8;
                        $chFeeAmount = $this -> getChain($message, $initPos, $finalPos); //Longitud 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $chFeeAmount;
                        break;
                    }
                    case 9:{ //Settlement Conversion Rate 
                        $initPos = $finalPos+1; $finalPos += 8;
                        $setConRate = $this -> getChain($message, $initPos, $finalPos); //Longitud 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $setConRate;
                        break;
                    }
                    case 10:{ //Cardholder Billing Conversion Rate
                        $initPos = $finalPos+1; $finalPos += 8;
                        $chBillConv = $this -> getChain($message, $initPos, $finalPos); //Longitud 9
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $chBillConv;
                        break;
                    }
                    case 11: { //System Trace Audit Number
                        $initPos = $finalPos+1; $finalPos += 6;
                        $sysTAN = $this -> getChain($message, $initPos, $finalPos); //Longitud: 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $sysTAN;
                        break;
                    }
                    case 12: { //Local Transaction Time
                        $initPos = $finalPos+1; $finalPos += 6;
                        $localTransactionTime = $this -> getChain($message, $initPos, $finalPos); //Longitud: 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $localTransactionTime;
                        break;
                    }
                    case 13: { //Local Tansaction Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $localTransactionDate = $this -> getChain($message, $initPos, $finalPos); //Longitud 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $localTransactionDate;
                        break;
                    }
                    case 14:{ //Expiration Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $expDate = $this -> getChain($message, $initPos, $finalPos); //Longitud 4
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $expDate;
                        break;
                    }
                    case 15: { //Settlement Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $setDate = $this -> getChain($message, $initPos, $finalPos); //Long 4
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $setDate;
                        break;
                    }
                    case 16: { //Conversion Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $convDate = $this -> getChain($message, $initPos, $finalPos); //Long 4
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $convDate;
                        break; 
                    }
                    case 17:{ //Capture Date
                        $initPos = $finalPos+1; $finalPos += 4;
                        $capDate = $this -> getChain($message, $initPos, $finalPos); //Long 4
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $capDate;
                        break;
                    }
                    case 18:{ //Merchant Type
                        $initPos = $finalPos+1; $finalPos += 4;
                        $merchantType = $this -> getChain($message, $initPos, $finalPos); //Long 4
                        $flag = DB::select('select id from merchant_type_catalog where id = ?', [$merchantType]);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(count($flag) !== 0){
                            $response[$counter] -> $value = $merchantType;
                        
                        }else{
                            $response[$counter] -> $value = $merchantType." error - este código no existe dentro del catálogo";
                        }
                        break;
                    }
                    case 19: { //Aqcuiring Institution Country Code
                        $initPos = $finalPos+1; $finalPos += 3;
                        $countryCode = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $countryCode;
                        break;
                    }
                    case 20: { //Country Code Primary Account Number Ext
                        $initPos = $finalPos+1; $finalPos += 3;
                        $countryCodeNumbExt = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $countryCodeNumbExt;
                        break;
                    }
                    case 21: { //Forwarding Institution Country Code
                        $initPos = $finalPos+1; $finalPos += 3;
                        $forwardCC = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $forwardCC;
                        break;
                    }
                    case 22:{ //POS Entry Mode
                        $initPos = $finalPos+1; $finalPos += 3;
                        $posEmode = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $emExist = DB::select("select * from entrymode where entry_mode = ?", [$posEmode]);
                        if(!empty($emExist)){
                            $response[$counter] -> $value = $posEmode;
                        }else{
                            $response[$counter] -> $value = "Error: No existe el valor dentro del catálogo de Entry Mode -> '".$posEmode."'";
                        }
                        break;
                    }
                    case 23: { //Card Sequence Number 
                        $initPos = $finalPos+1; $finalPos += 3;
                        $cardSeqNum = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $cardSeqNum;
                        break;
                    }
                    case 24: { //Network International Identifier
                        $initPos = $finalPos+1; $finalPos += 3;
                        $networkII = $this -> getChain($message, $initPos, $finalPos); //Long 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $networkII;
                        break;
                    }
                    case 25: { //POS Condition Code 
                        $initPos = $finalPos+1; $finalPos += 2;
                        $posConditionCode = $this -> getChain($message, $initPos, $finalPos); //Long 2
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $posConditionCode;
                        break;
                    }
                    case 26: { // POS PIN Code
                        $initPos = $finalPos+1; $finalPos += 2;
                        $pinCode = $this -> getChain($message, $initPos, $finalPos); //Long 2
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $pinCode;
                        break;
                    }
                    case 27: { //Auth Identification Response Length
                        $initPos = $finalPos+1; $finalPos += 1;
                        $authIdent = $this -> getChain($message, $initPos, $finalPos); //Long 1
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $authIdent;
                        break;
                    }
                    case 28: { //Transaction Fee Amount
                        $initPos = $finalPos+1; $finalPos += 9;
                        $tranFeeAm = $this -> getChain($message, $initPos, $finalPos); //Long 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $tranFeeAm;
                        break;
                    }
                    case 29: { //Settlement Fee Amount
                        $initPos = $finalPos+1; $finalPos += 8;
                        $setFeeAm = $this -> getChain($message, $initPos, $finalPos); //Long 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $setFeeAm;
                        break;
                    }
                    case 30: { //Transaction Proccesing Fee Amount
                        $initPos = $finalPos+1; $finalPos += 8;
                        $transFeeAmount = $this -> getChain($message, $initPos, $finalPos); //Long 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type]; 
                        $response[$counter] -> $value = $transFeeAmount;
                        break;
                    }
                    case 31: { //Settlement Processing Fee Amonut
                        $initPos = $finalPos+1; $finalPos += 8;
                        $setProFeeAmount = $this -> getChain($message, $initPos, $finalPos); //Long 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $setProFeeAmount;
                        break;
                    }
                    case 32:{ //Aqcuiring Institution ID Code
                        $initPos = $finalPos+1; $finalPos += 2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') <= 11){
                            $initPos = $finalPos+1; $finalPos += $len;
                            $aqrCode = $this -> getChain($message, $initPos, $finalPos); //Long 11
                            $response[$counter] -> $value = $aqrCode;
                        }else{
                            $initPos = $finalPos+1; $finalPos += ltrim($len, '0');
                            $aqrCode = $this -> getChain($message, $initPos, $finalPos); //Long 11
                            $response[$counter] -> $value = 'error tipo de dato no válido ->'.$aqrCode;
                            $i = 300;
                            break;
                        }
                        break;
                    }
                    case 33: { //Fordwardig Institution Identification Code
                        $initPos = $finalPos+1; $finalPos += 11;
                        $fordCode = $this -> getChain($message, $initPos, $finalPos); //Long 11
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $fordCode;
                        break;
                    }
                    case 34: { //Extending Primary Account Number
                        $initPos = $finalPos+1; $finalPos += 28;
                        $extAccNum = $this -> getChain($message, $initPos, $finalPos); //Long 28
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $extAccNum;
                        break;
                    }
                    case 35: { //Track 2 Data
                        $initPos = $finalPos+1; $finalPos += 2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && intval($len) <= 37){
                            $initPos = $finalPos+1; $finalPos += intval($len);
                            $track2Data = $this -> getChain($message, $initPos, $finalPos); //Long 37
                            $response[$counter] -> $value = $track2Data;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato inválido';
                        }
                        break;
                    }
                    case 36: { 
                        
                    }
                    case 37: { //Retrivel Reference Number
                        $initPos = $finalPos+1; $finalPos += 12;
                        $retRefNum = $this -> getChain($message, $initPos, $finalPos); //Long 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $retRefNum;
                        break;
                    }
                    case 38: { //Autorization Identification Response
                        $initPos = $finalPos+1; $finalPos += 6;
                        $authIDResp = $this -> getChain($message, $initPos, $finalPos); //Long 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $authIDResp;
                        break;
                    }
                    case 39: { //Response Code
                        $initPos = $finalPos+1; $finalPos += 2;
                        $respCode = $this -> getChain($message, $initPos, $finalPos); //Long 2
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $respCode;
                        break;
                    }
                    case 40: {
                        break;
                    }
                    case 41: { //Card Aceptor Terminal ID
                        $initPos = $finalPos+1; $finalPos += 16;
                        $cardAcTermID = $this -> getChain($message, $initPos, $finalPos); //Long 16
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $cardAcTermID;
                        break;
                    }
                    case 42: { //Card Acceptor ID Code
                        $initPos = $finalPos+1; $finalPos += 15;
                        $cardAcIDCode = $this -> getChain($message, $initPos, $finalPos); //Long 16
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $cardAcIDCode;
                        break;
                    }
                    case 43: { //Card Aceptor Name / Location
                        $initPos = $finalPos+1; $finalPos += 40;
                        $cardAcName = $this -> getChain($message, $initPos, $finalPos); //Long 40
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $cardAcName;
                        break;
                    }
                    case 44:{ //Additional Response Data
                        $initPos = $finalPos+1; $finalPos += 2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') <= 27){
                            $initPos = $finalPos+1; $finalPos += intval(ltrim($len, '0'));
                            $addRespData = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $addRespData;
                        }
                        else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 45: { //Track 1 Data
                        $initPos = $finalPos + 1; $finalPos += 2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        //return $len;
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') <= 76){
                            $initPos = $finalPos+1; $finalPos += intval(ltrim($len, '0'));
                            $track1 = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $track1;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 46: { // Iso Aditional Data
                        $initPos = $finalPos + 1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $initPos = $finalPos+1; $finalPos += intval(ltrim($len, '0'));
                        $ISOAdd = $this -> getChain($message, $initPos, $finalPos );
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $ISOAdd;
                        break;
                    }
                    case 47: {
                        break;
                    }
                    case 48: { //Retailer Data
                        //Primeras tres posiciones indica la longitud del campo
                        $initPos = $finalPos+1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') > 1){
                            $initPos = $finalPos+1; $finalPos += intval(ltrim($len, '0'));
                            $retailerData = $this -> getChain($message, $initPos, $finalPos); //Long 30
                            if(strpos($retailerData, '!')){
                                $response[$counter] -> $value = 'error - Error en el contenido del campo -> Posición: '.strpos($retailerData, '!');
                                $i = 300;
                                break;
                            }else{
                                $response[$counter] -> $value = $retailerData;
                            }
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
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
                            if($currCode == $data['CURRENCY_CODE']){ $flag = 1;}
                        }
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if($flag == 1){
                            //$initPos = $finalPos+1; $finalPos += 3;
                            $response[$counter] -> $value = $currCode;
                        }else{
                            //$initPos = $finalPos+1; $finalPos += 3;
                            $response[$counter] -> $value = $currCode.' error - no existe el dato';
                        }
                        break;
                    }
                    case 50: {
                        break;
                    }
                    case 51: {
                        break;
                    }
                    case 52: {  //PIN
                        $initPos = $finalPos+1; $finalPos += 16;
                        $pin = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $pin;
                        break;
                    }
                    case 53: { //Security Related Control Information
                        $initPos = $finalPos+1; $finalPos += 16;
                        $ContronInfo = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $ContronInfo;
                        break;
                    }
                    case 54: {
                        $initPos = $finalPos+1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $initPos = $finalPos+1; $finalPos += $len;
                        $AddAmounts = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $AddAmounts;
                        break;
                    }
                    case 55: {
                        break;
                    }
                    case 56: {
                        break;
                    }
                    case 57: {
                        break;
                    }
                    case 58: {
                        break;
                    }
                    case 59: {
                        break;
                    }
                    //TODO 51 - 59
                    case 60: { //Terminal Data
                        $initPos = $finalPos+1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') <= 19){
                            $initPos = $finalPos+1; $finalPos += $len;
                            $termData = $this -> getChain($message, $initPos, $finalPos); //Long 19
                            $response[$counter] -> $value = $termData;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 61: { //Response Code Data
                        $initPos = $finalPos+1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') <= 22){
                            $initPos = $finalPos+1; $finalPos += intval(ltrim($len, '0'));
                            $respCodeData = $this -> getChain($message, $initPos, $finalPos); //Long 22
                            $response[$counter] -> $value = $respCodeData;
                        }else{
                            $response[$counter] -> $value = 'eeror - tipo de dato no válido';
                        }
                        break;
                    }
                    case 62: { //Postal Code
                        $initPos = $finalPos+1; $finalPos += 3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') <= 13){
                            if(ltrim($len, '0') == 10){
                                $initPos = $finalPos+1; $finalPos += ltrim($len, '0');
                                $postalCode = $this -> getChain($message, $initPos, $finalPos);
                                $response[$counter] -> $value = $postalCode;
                            }else{
                                $response[$counter] -> $value = 'error - tipo de dato no válido';
                            }
                        }
                        break;
                    }
                    case 63: { //Aditional Data
                        $initPos = $finalPos+1; $finalPos += 3;
                        //Las primeras tres posiciones son la longitud del campo.
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        //Creación del objeto para la additional data
                        $counter++; $id++; 
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $additionalData = '';
                        if(is_numeric($len) && ltrim($len, '0') <= 0){
                            $additionalData = $this -> getChain($message, $initPos+3, $finalPos + intval($len));
                            $response[$counter] -> $value = $additionalData;
                            break;
                        }else{
                            $additionalData = $this -> getChain($message, $initPos+3, $finalPos + intval($len));
                            $response[$counter] -> $value = $additionalData;
                            //$finalPos += intval($len);
                            //break;
                        }
                        if($additionalData[0] === ' ' && $additionalData[1] === ' '){
                            $finalPos += intval($len);
                            break;
                        }
                        //Obtención del header para la lectura y desglose de los tokens
                        $initPos = $finalPos+1; $finalPos += 12; 
                        $headerAllTokens = $this -> getChain($message, $initPos, $finalPos);
                        //Creación del objeto para el header
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = 'P-63.0';
                        $response[$counter] -> $name = 'Additional Data Header';
                        $response[$counter] -> $type = 'ANS(12)';
                        $response[$counter] -> $value = $headerAllTokens;
                        //Validación del header
                        if($headerAllTokens[0] === '&' && $headerAllTokens[1] === ' '){ //Eye - Catcher
                                //Obtener el número de tokens que hay en el mensaje
                                $numberOfTokens = ltrim($this->getChain($headerAllTokens, 2, 6), '0') - 1;
                                $initPos = $finalPos + 1;
                                $finalPos += 10; //Tamaño del token header
                                //Se recorre la cadena del mensaje para la obtención de los tokens y desglosarlos
                                $counterField = '0'; //contador auxiliar para el desglose de los 'field' en los 
                                $counterTokens = 0;
                                //Obtener el numero de tokens 'reales' en el mensaje
                                for($h = 0; $h < strlen($additionalData); $h++){
                                    if($additionalData[$h] == "!"){
                                        $counterTokens++;
                                    }
                                }
                                if($numberOfTokens == $counterTokens){
                                    for ($x = 0; $x < $numberOfTokens; $x++) {
                                        $tokenHeader = '';
                                        $idToken = '';
                                        $lenToken = '';
                                        $tokenHeader = $this->getChain($message, $initPos, $finalPos);
                                        //Nombres para el objeto
                                        $idToken = $this->getChain($tokenHeader, 0, 3);
                                        $idTokenString = $this->getChain($idToken, 2, 3);
                                        $lenString = $this->getChain($idToken, 2, 3) . '-Longitud';
                                        $valueTokenString = $this->getChain($idToken, 2, 3) . '-Contenido';
                                        //Valores para el objeto
                                        $lenToken = $this->getChain($tokenHeader, 4, 8);
                                        $valueToken = $this->getChain($message, $finalPos + 1, $finalPos + intval(ltrim($lenToken, '0')));
                                        //Creación del objeto para los tokens
                                        //Primer campo -> identificador del token
                                        if($idToken[0] == '!' && $idToken[1] == ' '){
                                            $counterField++; $counter++; $id++;
                                            $response[$counter] = new stdClass();
                                            $response[$counter]->$number = $id;
                                            $response[$counter]->$field = 'P-63.' . $counterField;
                                            $response[$counter]->$name = $idTokenString;
                                            $response[$counter]->$type = '-';
                                            $response[$counter]->$value = $idToken;
                                        }else{
                                            $counterField++; $counter++; $id++;
                                            $response[$counter] = new stdClass();
                                            $response[$counter]->$number = $id;
                                            $response[$counter]->$field = 'P-63.' . $counterField;
                                            $response[$counter]->$name = 'Error Token';
                                            $response[$counter]->$type = '-';
                                            $response[$counter]->$value = "Error en Token: Existe un error en la estructura del header identificador del token ->'".$idToken."'";
                                            $x = 300; //Ciclo del desglose del token
                                            $i = 300; //ciclo general
                                        }
                                        
                                        //Segundo campo -> longitud del token
                                        $counter++;$id++;
                                        $response[$counter] = new stdClass();
                                        $response[$counter]->$number = $id;
                                        $response[$counter]->$field = 'P-63.' . $counterField;
                                        $response[$counter]->$name = $lenString;
                                        $response[$counter]->$type = 'N(4)';
                                        $response[$counter]->$value = $lenToken;
                                        //Tercer campo -> valor del token
                                        $counter++;$id++;
                                        $response[$counter] = new stdClass();
                                        $response[$counter]->$number = $id;
                                        $response[$counter]->$field = 'P-63.' . $counterField;
                                        $response[$counter]->$name = $valueTokenString;
                                        $response[$counter]->$type = 'ANS';
                                        //Aumento en las posiciones respectivas (en caso de que exista algún error para continuar con la lectura)
                                        if (strpos($valueToken, '!')) {
                                            $response[$counter]->$value = $valueToken.' error - contenido del token';
                                            $i = 300;
                                            break;
                                        } else {
                                            $response[$counter]->$value = $valueToken;
                                            $initPos = $finalPos + intval(ltrim($lenToken, '0')) + 1;
                                            if ($x === $numberOfTokens - 1) {
                                                $finalPos += intval(ltrim($lenToken, '0'));
                                            } else {
                                                $finalPos += 10 + intval(ltrim($lenToken, '0'));
                                            }
                                        }
                                    }
                                }else{
                                    $response[$counter] = new stdClass();
                                    $response[$counter]->$number = $id;
                                    $response[$counter]->$field = 'Error';
                                    $response[$counter]->$name = '----';
                                    $response[$counter]->$type = '----';
                                    $response[$counter]->$value = "Error: El número de tokens identificados no corresponde con lo que manifiesta el mensaje: \n".
                                    'Tokens identificados -> '.$counterTokens.' Tokens que indica el header -> '.$numberOfTokens;
                                    $i = 200; //Romper el ciclo for
                                    break;
                                }
                        }else{
                            $response[$counter] = new stdClass();
                            $response[$counter]->$number = $id;
                            $response[$counter]->$field = 'Error';
                            $response[$counter]->$name = '----';
                            $response[$counter]->$type = '----';
                            $response[$counter]->$value = 'Error: Existe un error dentro del header de los tokens ->'.$headerAllTokens;
                            $i = 300;
                        }
                        break;
                    }
                    case 70: { //Network Managment Info Code
                        $initPos = $finalPos+1; $finalPos += 3;
                        $netManagment = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $netManagment;
                        break;
                    }
                    case 90: {  //Original Data Elements
                        $initPos = $finalPos+1; $finalPos += 42;
                        $ogDataElm = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $ogDataElm;
                        break;
                    }
                    case 95: { //Replacement Amounts
                        $initPos = $finalPos+1; $finalPos += 42;
                        $repAmount = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $repAmount;
                        break;
                    }
                    case 100: { //Receiving Institution ID Code ***
                        $initPos = $finalPos+1; $finalPos+=2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && $len <= 11){
                            $initPos = $finalPos+1; $finalPos += intval($len);
                            $recInstIDCode = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $recInstIDCode;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 102: { //Account ID 1
                        $initPos = $finalPos+1; $finalPos+=2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') <= 28){
                            $initPos = $finalPos+1; 
                            $finalPos += $len;
                            $accID1 = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $accID1;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 103: { //Account ID 2
                        $initPos = $finalPos+1; $finalPos+=2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') <= 28){
                            $initPos = $finalPos+1; $finalPos += $len;
                            $accID2 = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $accID2;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 120: { //Administrative Token ***
                        $initPos = $finalPos+1; $finalPos+=3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && $len <= 153){
                            $initPos = $finalPos+1; $finalPos += intval(ltrim($len, '0'));
                            $adminToken = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $adminToken;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 121: { //AuthInd ***
                        $initPos = $finalPos+1; $finalPos+=3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && intval($len) <= 23){
                            $initPos = $finalPos+1; $finalPos += intval($len);
                            $authID = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $authID;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 123: { //Crypto Service Message
                        $initPos = $finalPos+1; $finalPos+=3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && $len <= 553){
                            $initPos = $finalPos+1; $finalPos += intval($len);
                            $cryptoServMess = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $cryptoServMess;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 124: { //Depository Type ***
                        $initPos = $finalPos+1; $finalPos+=3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && $len <= 687){
                            $initPos = $finalPos+1; $finalPos+= intval($len);
                            $depType = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $depType;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 125: { //POS Settlment Data ***
                        $initPos = $finalPos+1; $finalPos+=3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && intval($len) <= 375){
                            $initPos = $finalPos+1; $finalPos += intval($len);
                            $posSetData = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $posSetData;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 126: { //Base 24 - Additional Data
                        $initPos = $finalPos+1; $finalPos+=3;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        //Creación del additional data 126
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = '';
                        $addDataB24 = "";
                        if(is_numeric($len) && intval($len) === 0){
                            $addDataB24 = $this -> getChain($message, $initPos+3, $finalPos + intval($len));
                            $response[$counter] -> $value = $addDataB24;
                            break;
                        }
                        
                        if(is_numeric($len) && $len <= 800){
                            $addDataB24 = $this -> getChain($message, $initPos+3, $finalPos + intval($len));
                            $response[$counter] -> $value = $addDataB24;
                            //Creación del objeto header de los tokens
                            $initPos = $finalPos+1; $finalPos += 12; 
                            $headerAllTokens = $this -> getChain($message, $initPos, $finalPos);
                            $counter++; $id++;
                            if($headerAllTokens[0].$headerAllTokens[1].$headerAllTokens[2] === '000'){
                                break;
                            }
                            //Validación del header de los tokens
                            if($headerAllTokens[0] === '&' && $headerAllTokens[1] === ' '){
                                    $response[$counter] = new stdClass();
                                    $response[$counter] -> $number = $id;
                                    $response[$counter] -> $field = 'S-126.0';
                                    $response[$counter] -> $name = 'Additional Data Header';
                                    $response[$counter] -> $type = 'ANS(12)';
                                    $response[$counter]->$value = $headerAllTokens;
                                    $numberOfTokens = ltrim($this->getChain($headerAllTokens, 2, 6), '0') - 1;
                                    $initPos = $finalPos + 1;
                                    $finalPos += 10;
                                    $counterField = '0'; //Auxiliar para la contrucción del objeto
                                    $counterTokens = 0;
                                    //Obtener el numero de tokens 'reales' en el mensaje
                                    for($h = 0; $h < strlen($addDataB24); $h++){
                                        if($addDataB24[$h] == "!"){
                                            $counterTokens++;
                                        }
                                    }
                                    if($counterTokens == $numberOfTokens){
                                        for ($p = 0; $p < $numberOfTokens; $p++) {
                                            $tokenHeader = '';
                                            $idToken = '';
                                            $lenToken = '';
    
                                            $tokenHeader = $this->getChain($message, $initPos, $finalPos);
                                            $idToken = $this->getChain($tokenHeader, 0, 3);
                                            $idTokenString = $this->getChain($idToken, 2, 3);
                                            $lenString = $this->getChain($idToken, 2, 3) . '-Longitud';
                                            $valueTokenString = $this->getChain($idToken, 2, 3) . '-Contenido';
                                            $lenToken = $this->getChain($tokenHeader, 4, 8);
                                            $valueToken = $this->getChain($message, $finalPos + 1, $finalPos + intval(ltrim($lenToken, '0')));
                                            //Contrucción de los objetos de los tokens
                                            //Primer campo -> Identificador del token
                                            $counter++;
                                            $id++;
                                            $counterField++;
                                            if($idToken[0] === '!' && $idToken[1] === ' '){
                                                $response[$counter] = new stdClass();
                                                $response[$counter]->$number = $id;
                                                $response[$counter]->$field = 'S-126.' . $counterField;
                                                $response[$counter]->$name = $idTokenString;
                                                $response[$counter]->$type = '-';
                                                $response[$counter]->$value = $idToken;
                                            }else{
                                                $response[$counter] = new stdClass();
                                                $response[$counter]->$number = $id;
                                                $response[$counter]->$field = 'Error Token';
                                                $response[$counter]->$name = '-';
                                                $response[$counter]->$type = '-';
                                                $response[$counter]->$value = "Existe un error dentro de la estructura del header de identificación del token '".$idToken."'";
                                                $i = 300;
                                                $p = 300;
                                            }
                                            
                                            //Segundo campo -> longitud del token
                                            $counter++;
                                            $id++;
                                            $response[$counter] = new stdClass();
                                            $response[$counter]->$number = $id;
                                            $response[$counter]->$field = 'S-126.' . $counterField;
                                            $response[$counter]->$name = $lenString;
                                            $response[$counter]->$type = 'N(4)';
                                            $response[$counter]->$value = $lenToken;
                                            //Tercer campo -> valor del token
                                            $counter++;
                                            $id++;
                                            $response[$counter] = new stdClass();
                                            $response[$counter]->$number = $id;
                                            $response[$counter]->$field = 'S-126.' . $counterField;
                                            $response[$counter]->$name = $valueTokenString;
                                            $response[$counter]->$type = 'ANS';
                                            $response[$counter]->$value = '';
                                            if (strpos($valueToken, '!')) {
                                                $response[$counter]->$value = $valueToken . ' error - contenido del token';
                                                break;
                                            } else {
                                                $response[$counter]->$value = $valueToken;
                                                $initPos = $finalPos + intval(ltrim($lenToken, '0')) + 1;
                                                $finalPos += 10 + intval(ltrim($lenToken, '0'));
                                            }
                                        }
                                    }else{
                                        $response[$counter] = new stdClass();
                                        $response[$counter]->$number = $id;
                                        $response[$counter]->$field = 'Error';
                                        $response[$counter]->$name = '----';
                                        $response[$counter]->$type = '----';
                                        $response[$counter]->$value = 'Error: El número de tokens identificados no corresponde con lo que manifiesta el mensaje: Número Esperado: '.$numberOfTokens.' Número Obtenido: '.$counterTokens;
                                        break;
                                    }
                                    
                            }
                            else{
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }else{
            return '-1';
        }
        $responseJSON = json_decode(json_encode($response), true);
        return $responseJSON;
    }

    //Función para obtener el header del mensaje.
    function getChain($message, $initPos, $finalPos){
        $chain = '';
        for($i = $initPos; $i < $finalPos+1; $i++){
            if( !isset($message[$i]) ) $message[$i] = '*' ;
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

    //Se ovyiene el catálogo según las posiciones obtenidas en el bitmap
    public function getCatalog($values){
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
