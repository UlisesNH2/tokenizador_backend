<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class VisaController extends Controller{

    public function getBreakesVisa(Request $request){ //declarar la funcion para la peticion
        $message =  $request -> message;  
        $positions = array();
        $response = array();
        $initPos = 64; 
        $finalPos = 79; 
        //Variables de cada uno de los Campos
        $bitmapSecondary=''; 
        $bitmapSecundario= '';

        if($initMessage=substr($message, 0, 4)){ //Validación del numero 1601 inicio de mensajeria hexa en visa

                $header = $this -> getChain($message, 0, 43);
                //Extraer el tipo de mensaje 
                $typeMess = $this -> getChain($message, 44, 47);
                //Extraer el bitmap principal
                $mainBitmap = $this -> getChain($message, 48, 63);
                //Conversión a binario del bitmap principal
                $binaryBitmap = $this -> getBinary($mainBitmap, 0, 16);
                //Conversión a binario del principal bitmap
                $secondaryBitmap = $this -> getChain($message, 64, 79); 
                $secondaryBMValue = $this -> getBinary($secondaryBitmap, 0, 16);

                //Se ingresa al response los siguientes datos: Header, MTI, Bitmap Primario y Bitmap Secundario.

                $counter = 0; // Esta variable nos ayudará a indicar la cantidad de objetos que obtuvimos en el arreglo final.
                $response[$counter] = new stdClass();
                $response[$counter] -> message = $message;
                $response[$counter] -> header = $header;
                $response[$counter] -> MTI = $typeMess;
                $response[$counter] -> bitmap = $binaryBitmap.$secondaryBMValue;

                //Identificando qué campos están encendidos en el Bitmap Primario.
                for($i = 0; $i < strlen($binaryBitmap); $i++){
                    if($binaryBitmap[$i] === '1'){
                        array_push($positions, $i+1);
                    }
                }
                
                //Identificando qué campos están encendidos en el Bitmap Secundario.
                for($i = 0; $i < strlen($secondaryBMValue); $i++){
                    if($secondaryBMValue[$i] === '1'){
                        array_push($positions, $i+65); // El Bitmap Secundario comienza a partir del bit 65, por lo que se comienza a agregar desde ahí.
                    }
                }
                
                //Obtenemos el catálogo de acuerco con las posiciones que obtuvimos del Bitmap primario y secundario.
                $catalog = $this -> getCatalog($positions);
                //return $positions;

                $number = 'number';  $field = 'field'; $name= 'name'; $type = 'type'; $value = 'value'; $id=  3;

                // Validamos los Data Element
                for($i = 0; $i < count($positions); $i++){
                    switch($positions[$i]){
                        case 1:{ // Secondary Bitmap
                            $counter++; $id++;
                            $response[$counter] = new stdClass();
                            $response[$counter] -> $number = $id;
                            $response[$counter] -> $field = $catalog[$i]['field'];
                            $response[$counter] -> $name = $catalog[$i]['name'];
                            $response[$counter] -> $type = $catalog[$i]['type'];
                            $response[$counter] -> $value = $secondaryBMValue;
                            break;
                        }
                        case 2:{ // Primary Account Number
                            $initPos = $finalPos+3; $finalPos += 18;  
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
                        case 3:{ // Processing Code
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
                        case 4:{ // Amount
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
                        case 5:{ // Amount, Settlement
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
                        case 6:{ // Amount, Cardholder Billing
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
                        case 7:{ // Transmission Date and Time
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
                        case 8:{
                            break;
                        }
                        case 9:{ // Conversion Rate, Settlement
                            $initPos = $finalPos+2; $finalPos += 8;
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
                        case 10:{ // Conversion Rate, Cardholder Billing
                            $initPos = $finalPos+2; $finalPos += 8;
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
                        case 11:{ // System Trace Audit Number
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
                        case 12:{ // Time, Local Transaction
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
                        case 13:{ // Date, Local Transaction
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
                        case 14:{ // Date, Expiration
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
                        case 15:{ // Date, Settlement
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
                        case 16:{ // Date, Conversion
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
                        case 17:{ // Date, Capture
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
                        case 18:{ // Merchant Type
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
                        case 19:{ // Acquiring Institution Country Code
                            $initPos = $finalPos+1; $finalPos += 4;
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
                        case 20:{ // PAN Extended, Country Code
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
                        case 21:{
                            break;
                        }
                        case 22:{ // POS Entry Mode
                            $initPos = $finalPos+1; $finalPos += 3; // La longitud debe ser de 4
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
                        case 23:{ // Card Sequence Number
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
                        case 24:{
                            break;
                        }
                        case 25:{ // POS Condition Code
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
                        case 26:{ // POS PIN Capture Code
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
                        case 27:{
                            break;
                        }
                        case 28:{ // Amount, Transaction Fee
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
                        case 29:{
                            break;
                        }
                        case 30:{
                            break;
                        }
                        case 31:{
                            break;
                        }
                        case 32:{ // Acquiring Institution Identification Code - Es necesario validar los datos que se tienen
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
                                $i = 13;
                                break;
                            }
                            break;
                        }
                        case 33:{ // Forwarding Institution Identification Code
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
                        case 34:{ // Acceptance Environment Data (TLV Format)

                        }
                    }
                }

        }else{
            return -1;
        }
        $responseJSON = json_decode(json_encode($response), true);
        return $responseJSON;
    }

    // Con esta función obtenemos la cadena de caracteres que se encuentra entre dos posiciones
    function getChain($message, $initPos, $finalPos){
        $chain = '';
        for ($i = $initPos; $i < $finalPos+1; $i++){
            if( !isset($message[$i]) ) $message[$i] ='*';
            $chain .= $message[$i];
        }
        return $chain;
    }

    // Con esta función convertimos nuestra cadena obtenida en la función anterior a Binario
    function getBinary($bitmap, $initPos, $finalPos){
        $binaryVal = array();
        $positions= '';
        for ($i = $initPos; $i < $finalPos; $i++){
            array_push($binaryVal, str_pad(base_convert($bitmap[$i], 16, 2), 4, '0', STR_PAD_LEFT));
        }
        for($i = 0; $i < count($binaryVal); $i++){
            $positions .= $binaryVal[$i];
        }
        return $positions;
    }

    // Con esta función obtenemos el valor de cada campo
    public function getCatalog($values){
        $catalog = array();
        $response = array();
        
        for ($i = 0; $i < count($values); $i++){
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