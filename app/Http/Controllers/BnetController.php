<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class BnetController extends Controller{

    public function bnetMessage(Request $request){
        $message = $request -> message;
        $positions = array();
        $response = array();
        $initPos=32;
        $finalPos=40;

        //Creo que esto puede ser una gran aportación al proyecto

        $bitmapSecondary=''; 
        $bitmapSecundario= '';

        if ($mti=substr($message, 0, 8)){ // Aquí sería 4 en lugar de 8

            //Extraemos el Bitmap Primario que está en Hexa
            $bitmapPrimary = $this -> getChain($message, 8, 24);
            //Convertimos el Bitmap primario de Hexa a Binario
            $bitmapPrimario = $this -> getBinary($bitmapPrimary, 0, 16);
            //Extraemos el Bitmap secundario en Hexa
            $bitmapSecondary = $this -> getChain($message, 24, 40);
            //Convertimos el Bitmap secundario de Hexa a Binario
            $bitmapSecundario = $this -> getBinary($bitmapSecondary, 0, 16);
            
            // Aquí mostramos lo que hemos obtenido de cada validación: MTI, Bitmap primario y Bitmap secundario

            $counter = 0; // Esta variable nos ayudará a indicar la cantidad de objetos que obtuvimos en el arreglo final.
            $response[$counter] = new stdClass();
            $response[$counter] -> message = $message;
            $response[$counter] -> MTI = $mti;
            $response[$counter] -> Bitmap = $bitmapPrimario.$bitmapSecundario;

            // Identificamos qué campos se habilitarán en el Bitmap Primario
            for ($i = 0; $i < strlen($bitmapPrimario); $i++){
                if($bitmapPrimario[$i] === '1'){
                    array_push($positions, $i+1);
                }
            }
            
            // Identificamos qué campos se habilitarán en el Bitmap Secundario
            for($i = 0; $i < strlen($bitmapSecundario); $i++){
                if($bitmapSecundario[$i] === '1'){
                    array_push($positions, $i+65); // El Bitmap Secundario comienza a partir del bit 65, por lo que se comienza a agregar desde esta posición.
                }
            }

            // Primero obtenemos el catálogo de acuerdo con las posiciones que obtuvimos en nuestro Bitmap
            $catalog = $this -> getCatalog($positions);
            //return $positions; // Con este return observamos solamente qué posiciones nos arrojó el catálogo, más no arroja información de los campos.

            //Variables para guardar la información de cada DE que obtengamos.
            $number = 'number'; $field = 'field'; $name= 'name'; $type = 'type'; $value = 'value'; $id=  2;
            
            //Validación de los Data Elements
            for($i = 0; $i < count($positions); $i++){
                switch($positions[$i]){
                    case 1:{ // DE 1 - Bit Map, Secondary
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i]['field'];
                        $response[$counter] -> $name = $catalog[$i]['name'];
                        $response[$counter] -> $type = $catalog[$i]['type'];
                        $response[$counter] -> $value = $bitmapSecundario;
                        break;
                    }
                    case 2:{// DE 2 - Primary Account Number
                        $initPos = $finalPos+4; $finalPos += 35;  
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
                    case 3:{ // DE 3 - Processing Code
                        $initPos = $finalPos+1; $finalPos += 12;
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
                    case 4:{ // DE 4 - Amount, Transaction
                        $initPos = $finalPos+1; $finalPos += 24;
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
                    case 5:{ // DE 5 - Amount, Settlement
                        $initPos = $finalPos+1; $finalPos += 24;
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
                    case 6:{ // DE 6 - Amount, Cardholder Billing
                        $initPos = $finalPos+1; $finalPos += 24;
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
                    case 7:{ // DE 7 - Transmission Date and Time
                        $initPos = $finalPos+1; $finalPos += 20;
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
                    case 8:{ // DE 8 - Amount, Cardholder Billing Fee
                        $initPos = $finalPos+1; $finalPos += 16;
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
                    case 9:{ // DE 9 - Conversion Rate, Settlement
                        $initPos = $finalPos+1; $finalPos += 16;
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
                    case 10:{ // DE 10 - Conversion Rate, Cardholder Billing
                        $initPos = $finalPos+1; $finalPos += 16;
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
                    case 11:{ // DE 11 - System Trace Audit Number (STAN)
                        $initPos = $finalPos+1; $finalPos += 12;
                        $stan = $this -> getChain($message, $initPos, $finalPos); //Longitud: 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $stan;
                        break;
                    }
                    case 12:{ // DE 12 - Time, Local Transaction
                        $initPos = $finalPos+1; $finalPos += 12;
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
                    case 13:{ // DE 13 - Date, Local Transaction
                        $initPos = $finalPos+1; $finalPos += 8;
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
                    case 14:{ // DE 14 - Date, Expiration
                        $initPos = $finalPos+1; $finalPos += 8;
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
                    case 15:{ // DE 15 - Date, Settlement
                        $initPos = $finalPos+1; $finalPos += 8;
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
                    case 16:{ // DE 16 - Date, Conversion
                        $initPos = $finalPos+1; $finalPos += 8;
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
                    case 17:{ // DE 17 - Date, Capture
                        $initPos = $finalPos+1; $finalPos += 8;
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
                    case 18:{ // DE 18 - Merchant Type
                        $initPos = $finalPos+1; $finalPos += 8;
                        $merchantType = $this -> getChain($message, $initPos, $finalPos); //Longitud 4
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
                    case 19:{ // DE 19 - Acquiring Institution Country Code
                        $initPos = $finalPos+1; $finalPos += 6;
                        $countryCode = $this -> getChain($message, $initPos, $finalPos); //Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $countryCode;
                        break;
                    }
                    case 20:{ // DE 20 - Primary Account Number (PAN) Country Code
                        $initPos = $finalPos+1; $finalPos += 6;
                        $panCountryCode = $this -> getChain($message, $initPos, $finalPos); //Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $panCountryCode;
                        break;
                    }
                    case 21:{ // DE 21 - Forwarding Institution Country Code
                        $initPos = $finalPos+1; $finalPos += 6;
                        $forInsCounCode = $this -> getChain($message, $initPos, $finalPos); //Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $forInsCounCode;
                        break;
                    }
                    case 22:{ // DE 22 - Point-of-Service [POS] Entry Mode
                        $initPos = $finalPos+1; $finalPos += 6;
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
                    case 23:{ // DE 23 - Card Sequence Number
                        $initPos = $finalPos+1; $finalPos += 6;
                        $cardSeqNum = $this -> getChain($message, $initPos, $finalPos); //Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $cardSeqNum;
                        break;
                    }
                    case 24:{ // DE 24 - Network International ID
                        $initPos = $finalPos+1; $finalPos += 6;
                        $networkIntId = $this -> getChain($message, $initPos, $finalPos); //Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $networkIntId;
                        break;
                    }
                    case 25:{ // DE 25 - Point-of-Service (POS) Condition Code
                        $initPos = $finalPos+1; $finalPos += 4;
                        $posConditionCode = $this -> getChain($message, $initPos, $finalPos); //Longitud 2
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $posConditionCode;
                        break;
                    }
                    case 26:{ // DE 26 - Point-of-Service (POS) Personal ID Number (PIN) Capture Code
                        $initPos = $finalPos+1; $finalPos += 4;
                        $posPinCapCode = $this -> getChain($message, $initPos, $finalPos); //Longitud 2
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $posPinCapCode;
                        break;
                    }
                    case 27:{ // DE 27 - Authorization ID Response Length
                        $initPos = $finalPos+1; $finalPos += 2;
                        $authIdRes = $this -> getChain($message, $initPos, $finalPos); //Longitud 1
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $authIdRes;
                        break;
                    }
                    case 28:{ // DE 28 - Amount, Transaction Fee
                        $initPos = $finalPos+1; $finalPos += 18;
                        $amountTranFee = $this -> getChain($message, $initPos, $finalPos); //Longitud 9
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $amountTranFee;
                        break;
                    }
                    case 29:{ // DE 29 - Amount, Settlement Fee
                        $initPos = $finalPos+1; $finalPos += 18;
                        $amountSetFee = $this -> getChain($message, $initPos, $finalPos); //Longitud 9
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $amountSetFee;
                        break;
                    }
                    case 30:{ // DE 30 - Amount, Transaction Processing Fee
                        $initPos = $finalPos+1; $finalPos += 18;
                        $amountTranProFee = $this -> getChain($message, $initPos, $finalPos); //Longitud 9
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type]; 
                        $response[$counter] -> $value = $amountTranProFee;
                        break;
                    }
                    case 31:{ // DE 31 - Amount, Settlement Processing Fee
                        $initPos = $finalPos+1; $finalPos += 18;
                        $amountSetProFee = $this -> getChain($message, $initPos, $finalPos); //Longitud 9
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $amountSetProFee;
                        break;
                    }
                    case 32:{ // DE 32 - Acquiring Institution ID Code
                        $initPos = $finalPos+5; $finalPos += 16;
                        $acqInstId = $this -> getChain($message, $initPos, $finalPos); // Longitud 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $acqInstId;
                        break;
                    }
                    case 33:{ // DE 33 - Forwarding Institution ID Code
                        $initPos = $finalPos+1; $finalPos += 12;
                        $forInstId = $this -> getChain($message, $initPos, $finalPos); // Longitud 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $forInstId;
                        break; 
                    }
                    case 34:{ // DE 34 - Primary Account Number (PAN), Extended
                        $initPos = $finalPos+1; $finalPos += 56;
                        $panExt = $this -> getChain($message, $initPos, $finalPos); //Longitud 28
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $panExt;
                    }
                    case 35:{ // DE 35 - Track 2 Data
                        $initPos = $finalPos+1; $finalPos += 2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && intval($len) <= 74){
                            $initPos = $finalPos+1; $finalPos += intval($len);
                            $track2Data = $this -> getChain($message, $initPos, $finalPos); //Longitud 37
                            $response[$counter] -> $value = $track2Data;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato inválido';
                        }
                        break;
                    }
                    case 36:{ // DE 36 - Track 3 Data
                        $initPos = $finalPos+1; $finalPos += 2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && intval($len) <= 208){
                            $initPos = $finalPos+1; $finalPos += intval($len);
                            $track3Data = $this -> getChain($message, $initPos, $finalPos); // Longitud 104
                            $response[$counter] -> $value = $track3Data;
                        }else{
                            $response[$counter] -> $value = 'error - tipo de dato inválido';
                        }
                        break;
                    }
                    case 37:{ // DE 37 - Retrieval Reference Number
                        $initPos = $finalPos+1; $finalPos += 24;
                        $retRefNum = $this -> getChain($message, $initPos, $finalPos); // Longitud 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $retRefNum;
                        break;
                    }
                    case 38:{ // DE 38 - Authorization ID Response
                        $initPos = $finalPos+1; $finalPos += 12;
                        $authIdResp = $this -> getChain($message, $initPos, $finalPos); // Longitud 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $authIdResp;
                        break;
                    }
                    case 39:{ // DE 39 - Response Code
                        $initPos = $finalPos+1; $finalPos += 4;
                        $respCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 2
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $respCode;
                        break;
                    }
                    case 40:{ // DE 40 - Service Restriction Code ** Validar en catálogo **
                        $initPos = $finalPos+1; $finalPos += 6;
                        $servRestCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $servRestCode;
                        break;
                    }
                    case 41:{ // DE 41 - Card Acceptor Terminal ID
                        $initPos = $finalPos+1; $finalPos += 16;
                        $cardAcTermId = $this -> getChain($message, $initPos, $finalPos); //Longitud 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $cardAcTermId;
                        break;
                    }
                    case 42:{ // DE 42 - Card Acceptor ID Code
                        $initPos = $finalPos+1; $finalPos += 30;
                        $cardAcIdCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 15
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $cardAcIdCode;
                        break;
                    }
                    case 43:{ // DE 43 - Card Acceptor Name/Location for All Transactions
                        $initPos = $finalPos+1; $finalPos += 80;
                        $cardAcName = $this -> getChain($message, $initPos, $finalPos); // Longitud 40
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $cardAcName;
                        break;
                    }
                    case 44:{ // DE 44 - Additional Response Data
                        $initPos = $finalPos+1; $finalPos += 2;
                        $len = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        if(is_numeric($len) && ltrim($len, '0') <= 25){ // Longitud 25
                            $initPos = $finalPos+1; $finalPos += intval(ltrim($len, '0'));
                            $addRespData = $this -> getChain($message, $initPos, $finalPos);
                            $response[$counter] -> $value = $addRespData;
                        }
                        else{
                            $response[$counter] -> $value = 'error - tipo de dato no válido';
                        }
                        break;
                    }
                    case 45:{ // DE 45 - Track 1 Data
                        $initPos = $finalPos+1; $finalPos +=152;
                        $track1Data = $this -> getChain($message, $initPos, $finalPos); // Longitud 76
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $track1Data;
                        break;
                    }
                    case 46:{ // DE 46 - Expanded Additional Amounts ** Validar en catálogo **
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $expAddAmounts = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $expAddAmounts;
                        break;
                    }
                    case 47:{ // DE 47 - Additional Data - National Use ** Validar en catálogo **
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $addDataNatUse = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $addDataNatUse;
                        break;
                    }
                    case 48:{ // DE 48 - Additional Data - Private Use ** Validar en catálogo y validar con el manual **
                        $initPos = $finalPos+7; $finalPos += 32;
                        $addDataPrivUse = $this -> getChain($message, $initPos, $finalPos); // Longitud 13
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $addDataPrivUse;
                        break;
                    }
                    case 49:{ // DE 49 - Currency Code, Transaction ** Validar valores en catálogo **
                        $flag = 0;
                        $initPos = $finalPos+1; $finalPos += 6;
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
                    case 50:{ // DE 50 - Currency Code, Settlement ** Validar si es necesario agregar otro apartado en el catálogo **
                        $initPos = $finalPos+1; $finalPos += 6;
                        $currCodeSet = $this -> getChain($message, $initPos, $finalPos);
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $currCodeSet;
                        break;
                    }
                    case 51:{ // DE 51 - Currency Code, Cardholder Billing ** Validar si es necesario agregar otro apartado en el catálogo **
                        $initPos = $finalPos+1; $finalPos += 6;
                        $currCodeBill = $this -> getChain($message, $initPos, $finalPos); // Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $currCodeBill;
                        break;
                    }
                    case 52:{ // DE 52 - Personal ID Number (PIN) Data
                        $initPos = $finalPos+1; $finalPos += 16;
                        $pinData = $this -> getChain($message, $initPos, $finalPos); // Longitud 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $pinData;
                        break;
                    }
                    case 53:{ // DE 53 - Security-Related Control Information
                        $intPos = $finalPos+1; $finalPos += 32;
                        $secRelControlInfo = $this -> getChain($message, $initPos, $finalPos); // Longitud 16
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $secRelControlInfo;
                        break;
                    }
                    case 54:{ // DE 54 - Additional Amounts
                        $initPos = $finalPos+1; $finalPos += 480;
                        $addAmounts = $this -> getChain($message, $initPos, $finalPos); // Longitud 240
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $addAmounts;
                        break;
                    }
                    case 55:{ // DE 55 - Integrated Circuit Card (ICC) System-Related Data
                        $initPos = $finalPos+1; $finalPos += 510;
                        $iccSysRelData = $this -> getChain($message, $initPos, $finalPos); // Longitud 255
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $iccSysRelData;
                        break;
                    }
                    case 56:{ // DE 56 - Payment Account Data
                        $initPos = $finalPos+1; $finalPos += 74;
                        $payAccData = $this -> getChain($message, $initPos, $finalPos); // Longitud 37
                        $counter++; $i++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $payAccData;
                        break;
                    }
                    case 57:{ // DE 57 - Reserved for National Use
                        break;
                    }
                    case 58:{ // DE 58 - Reserved for National Use
                        break;
                    }
                    case 59:{ // DE 59 - Reserved for National Use
                        break;
                    }
                    case 60:{ // DE 60 - Advice Reason Code 
                        $initPos = $finalPos+1; $finalPos += 120;
                        $advReasonCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 60
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $advReasonCode;
                        break;
                    }
                    case 61:{ // DE 61 - Point-of-Service [POS] Data
                        $initPos = $finalPos+1; $finalPos += 46;
                        $posData = $this -> getChain($message, $initPos, $finalPos); // Longitud 23
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $posData;
                        break;
                    }
                    case 62:{ // DE 62 - Intermediate Network Facility (INF) Data
                        $initPos = $finalPos+1; $finalPos += 200;
                        $infData = $this -> getChain($message, $initPos, $finalPos); // Longitud 100
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $infData;
                        break;
                    }
                    case 63:{ // DE 63 - Network Data
                        $initPos = $finalPos+1; $finalPos += 100;
                        $networkData = $this -> getChain($message, $initPos, $finalPos); // Longitud 50
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $networkData;
                        break;
                    }
                    case 64:{ // DE 64 - Message Authentication Code
                        $initPos= $finalPos+1; $finalPos += 16;
                        $messageAuthCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $messageAuthCode;
                        break;
                    }
                    case 65:{ // DE 65 - Bit Map, Extended
                        $initPos= $finalPos+1; $finalPos += 16;
                        $bitmapExt = $this -> getChain($message, $initPos, $finalPos); // Longitud 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $bitmapExt;
                    }
                    case 66:{ // DE 66 - Settlement Code
                        $initPos= $finalPos+1; $finalPos += 2;
                        $settlementCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 1
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $settlementCode;
                        break;
                    }
                    case 67:{ // DE 67 - Extended Payment Code
                        $initPos= $finalPos+1; $finalPos += 4;
                        $extPaymentCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 2
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $extPaymentCode;
                        break;
                    }
                    case 68:{ // DE 68 - Receiving Institution Country Code
                        $initPos= $finalPos+1; $finalPos += 6;
                        $recInstitutionCountryCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $recInstitutionCountryCode;
                        break;
                    }
                    case 69:{ // DE 69 - Settlement Institution Country Code
                        $initPos= $finalPos+1; $finalPos += 6;
                        $setInstitutionCounCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $setInstitutionCounCode;
                        break;
                    }
                    case 70:{ // DE 70 - Network Management Information Code
                        $initPos= $finalPos+1; $finalPos += 6;
                        $netManagementInfoCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 3
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $netManagementInfoCode;
                        break;
                    }
                    case 71:{ // DE 71 - Message Number
                        $initPos = $finalPos+1; $finalPos += 8;
                        $messageNumber = $this -> getChain($message, $initPos, $finalPos); // Longitud 4
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $messageNumber;
                        break;
                    }
                    case 72:{ // DE 72 - Message Number Last
                        $initPos= $finalPos+1; $finalPos += 8;
                        $messageNumberLast = $this -> getChain($message, $initPos, $finalPos); // Longitud 4
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $messageNumberLast;
                        break;
                    }
                    case 73:{ // DE 73 - Date, Action
                        $initPos= $finalPo+1; $finalPos += 12;
                        $dateAction = $this -> getChain($message, $initPos, $finalPos); // Longitud 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $dateAction;
                        break;
                    }
                    case 74:{ // DE 74 - Credits, Number
                        $initPos= $finalPos+1; $finalPos += 20;
                        $creditsNumber = $this -> getChain($message, $initPos, $finalPos); // Longitud 10
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $creditsNumber;
                        break;
                    }
                    case 75:{ // DE 75 - Credits, Reversal Number
                        $initPos = $finalPos+1; $finalPos += 20;
                        $creditsReversalNumber = $this -> getChain($message, $initPos, $finalPos); // Longitud 10
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $creditsReversalNumber;
                        break;
                    }
                    case 76:{ // DE 76 - Debits, Number
                        $initPos = $finalPos+1; $finalPos += 20;
                        $debitsNumber = $this -> getChain($message, $initPos, $finalPos); // Longitud 10
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $debitsNumber;
                        break;
                    }
                    case 77:{ // DE 77 - Debits, Reversal Number
                        $initPos = $finalPos+1; $finalPos += 20;
                        $debitsReversalNumber = $this -> getChain($message, $initPos, $finalPos); // Longitud 10
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $debitsReversalNumber;
                        break;
                    }
                    case 78:{ // DE 78 - Transfers, Number
                        $initPos = $finalPos+1; $finalPos += 20;
                        $transfersNumber = $this -> getChain($message, $initPos, $finalPos); // Longitud 10
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $transfersNumber;
                        break;
                    }
                    case 79:{ // DE 79 - Transfers, Reversal Number
                        $initPos = $finalPos+1; $finalPos += 20;
                        $transfersReversalNumber = $this -> getChain($message, $initPos, $finalPos); // Longitud 10
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $transfersReversalNumber;
                        break;
                    }
                    case 80:{ // DE 80 - Inquiries, Number
                        $initPos = $finalPos+1; $finalPos += 20;
                        $inquiriesNumber = $this -> getChain($message, $initPos, $finalPos); // Longitud 10
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $inquiriesNumber;
                        break;
                    }
                    case 81:{ // DE 81 - Authorizations, Number
                        $initPos = $finalPos+1; $finalPos += 20;
                        $authorizationsNumber = $this -> getChain($message, $initPos, $finalPos); // Longitud 10
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $authorizationsNumber;
                        break;
                    }
                    case 82:{ // DE 82 - Credits, Processing Fee Amount
                        $initPos = $finalPos+1; $finalPos += 24;
                        $creditsProcessingFeeAmount = $this -> getChain($message, $initPos, $finalPos); // Longitud 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $creditsProcessingFeeAmount;
                        break;
                    }
                    case 83:{ // DE 83 - Credits, Transaction Fee Amount
                        $initPos = $finalPos+1; $finalPos += 24;
                        $creditsTransactionFeeAmount = $this -> getChain($message, $initPos, $finalPos); // Longitud 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $creditsTransactionFeeAmount;
                        break;
                    }
                    case 84:{ // DE 84 - Debits, Processing Fee Amount
                        $initPos = $finalPos+1; $finalPos += 24;
                        $debitsProcessingFeeAmount = $this -> getChain($message, $initPos, $finalPos); // Longitud 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $debitsProcessingFeeAmount;
                        break;
                    }
                    case 85:{ // DE 85 - Debits, Transaction Fee Amount
                        $initPos = $finalPos+1; $finalPos += 24;
                        $debitsTransactionFeeAmount = $this -> getChain($message, $initPos, $finalPos); // Longitud 12
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $debitsTransactionFeeAmount;
                        break;
                    }
                    case 86:{ // DE 86 - Credits, Amount
                        $initPos = $finalPos+1; $finalPos += 32;
                        $creditsAmount = $this -> getChain($message, $initPos, $finalPos); // Longitud 16
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $creditsAmount;
                        break;
                    }
                    case 87:{ // DE 87 - Credits, Reversal Amount
                        $initPos = $finalPos+1; $finalPos += 32;
                        $creditsReversalAmount = $this -> getChain($message, $initPos, $finalPos); // Longitud 16
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $creditsReversalAmount;
                        break;
                    }
                    case 88:{ // DE 88 - Debits, Amount
                        $initPos = $finalPos+1; $finalPos += 32;
                        $debitsAmount = $this -> getChain($message, $initPos, $finalPos); // Longitud 16
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $debitsAmount;
                        break;
                    }
                    case 89:{ // DE 89 - Debits, Reversal Amount
                        $initPos = $finalPos+1; $finalPos += 32;
                        $debitsReversalAmount = $this -> getChain($message, $initPos, $finalPos); // Longitud 16
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $debitsReversalAmount;
                        break;
                    }
                    case 90:{ // DE 90 - Original Data Elements
                        $initPos = $finalPos+1; $finalPos += 84;
                        $originalDataElements = $this -> getChain($message, $initPos, $finalPos); // Longitud 42
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $originalDataElements;
                        break;
                    }
                    case 91:{ // DE 91 - Issuer File Update Code
                        $initPos = $finalPos+1; $finalPos += 2;
                        $fileUpdateCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 1
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $fileUpdateCode;
                        break;
                    }
                    case 92:{ // DE 92 - File Security Code
                        $initPos = $finalPos+1; $finalPos += 4;
                        $fileSecurityCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 2
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $fileSecurityCode;
                        break;
                    }
                    case 93:{ // DE 93 - Response Indicator
                        $initPos = $finalPos+1; $finalPos += 10;
                        $responseIndicator = $this -> getChain($message, $initPos, $finalPos); // Longitud 5
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $responseIndicator;
                        break;
                    }
                    case 94:{ // DE 94 - Service Indicator
                        $initPos = $finalPos+1; $finalPos += 14;
                        $serviceIndicator = $this -> getChain($message, $initPos, $finalPos); // Longitud 7
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $serviceIndicator;
                        break;
                    }
                    case 95:{ // DE 95 - Replacement Amounts
                        $initPos = $finalPos+1; $finalPos += 84;
                        $replacementAmounts = $this -> getChain($message, $initPos, $finalPos); // Longitud 32
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $replacementAmounts;
                        break;
                    }
                    case 96:{ // DE 96 - Message Security Code
                        $initPos = $finalPos+1; $finalPos += 16;
                        $messageSecurityCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $messageSecurityCode;
                        break;
                    }
                    case 97:{ // DE 97 - Amount, Net Settlement
                        $initPos = $finalPos+1; $finalPos += 34;
                        $netSettlementAmount = $this -> getChain($message, $initPos, $finalPos); // Longitud 17
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $netSettlementAmount;
                        break;
                    }
                    case 98:{ // DE 98 - Payee
                        $initPos = $finalPos+1; $finalPos += 50;
                        $payee = $this -> getChain($message, $initPos, $finalPos); // Longitud 25
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $payee;
                        break;
                    }
                    case 99:{ // DE 99 - Settlement Institution ID Code
                        $initPos = $finalPos+1; $finalPos += 22;
                        $settlementInstitutionIdentificationCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 11
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $settlementInstitutionIdentificationCode;
                        break;
                    }
                    case 100:{ // DE 100 - Receiving Institution ID Code
                        $initPos = $finalPos+1; $finalPos += 22;
                        $receivingInstitutionIdentificationCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 11
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $receivingInstitutionIdentificationCode;
                        break;
                    }
                    case 101:{ // DE 101 - File Name
                        $initPos = $finalPos+1; $finalPos += 34;
                        $fileName = $this -> getChain($message, $initPos, $finalPos); // Longitud 17
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $fileName;
                        break;
                    }
                    case 102:{ // DE 102 - Account ID 1
                        $initPos = $finalPos+1; $finalPos += 56;
                        $accountIdentification1 = $this -> getChain($message, $initPos, $finalPos); // Longitud 28
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $accountIdentification1;
                        break;
                    }
                    case 103:{ // DE 103 - Account ID 2
                        $initPos = $finalPos+1; $finalPos += 56;
                        $accountIdentification2 = $this -> getChain($message, $initPos, $finalPos); // Longitud 28
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $accountIdentification2;
                        break;
                    }
                    case 104:{ // DE 104 - Digital Payment Data
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $digitalPaymentData = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $digitalPaymentData;
                        break;
                    }
                    case 105:{ // Reserved for MasterCard Use
                        break;
                    }
                    case 106:{ // Reserved for MasterCard Use
                        break;
                    }
                    case 107:{ // Reserved for MasterCard Use
                        break;
                    }
                    case 108:{ // DE 108 - Additional Transaction Reference Data
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $additionalTransactionReferenceData = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $additionalTransactionReferenceData;
                        break;
                    }
                    case 109:{ // DE 109 - Reserved for ISO Use
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $reservedForISOUse = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $reservedForISOUse;
                        break;
                    }
                    case 110:{ // DE 110 - Additional Data–2
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $additionalData2 = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $additionalData2;
                        break;
                    }
                    case 111:{ // DE 111- Reserved for ISO Use 2
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $reservedForISOUse2 = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $reservedForISOUse2;
                        break;
                    }
                    case 112:{ // DE 112 - Additional Data (National Use)
                        $initPos = $finalPos+1; $finalPos += 200;
                        $additionalDataNationalUse = $this -> getChain($message, $initPos, $finalPos); // Longitud 100 - for global usage **
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $additionalDataNationalUse;
                        break;
                    }
                    case 113:{ // DE 113 - Reserved for National Use
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $reservedForNationalUse = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $reservedForNationalUse;
                        break;
                    }
                    case 114:{ // DE 114 - Reserved for National Use
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $reservedForNationalUse2 = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $reservedForNationalUse2;
                        break;
                    }
                    case 115:{ // DE 115 - Reserved for National Use
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $reservedForNationalUse3 = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $reservedForNationalUse3;
                        break;
                    }
                    case 116:{ // DE 116 - Reserved for National Use
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $reservedForNationalUse4 = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $reservedForNationalUse4;
                        break;
                    }
                    case 117:{ // DE 117 - Reserved for National Use
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $reservedForNationalUse5 = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $reservedForNationalUse5;
                        break;
                    }
                    case 118:{ // DE 118 - Reserved for National Use
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $reservedForNationalUse6 = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $reservedForNationalUse6;
                        break;
                    }
                    case 119:{ // DE 119 - Reserved for National Use
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $reservedForNationalUse7 = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $reservedForNationalUse7;
                        break;
                    }
                    case 120:{ // DE 120 - Record Data
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $recordData = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $recordData;
                        break;
                    }
                    case 121:{ // DE 121 - Authorizing Agent ID Code
                        $initPos = $finalPos+1; $finalPos += 12;
                        $authorizingAgentIDCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 6
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $authorizingAgentIDCode;
                        break;
                    }
                    case 122:{ // DE 122 - Additional Record Data
                        $initPos = $finalPos+1; $finalPos += 1998;
                        $additionalRecordData = $this -> getChain($message, $initPos, $finalPos); // Longitud 999
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $additionalRecordData;
                        break;
                    }
                    case 123:{ // DE 123 - Receipt Free Text
                        $initPos = $finalPos+1; $finalPos += 1024;
                        $receiptFreeText = $this -> getChain($message, $initPos, $finalPos); // Longitud 512
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $receiptFreeText;
                        break;
                    }
                    case 124:{ // DE 124 - Member-Defined Data
                        $initPos = $finalPos+1; $finalPos += 598;
                        $memberDefinedData = $this -> getChain($message, $initPos, $finalPos); // Longitud 299
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $memberDefinedData;
                        break;
                    }
                    case 125:{ // DE 125 - New PIN Data
                        $initPos = $finalPos+1; $finalPos += 16;
                        $newPINData = $this -> getChain($message, $initPos, $finalPos); // Longitud 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $newPINData;
                        break;
                    }
                    case 126:{ // DE 126 - Private Data
                        $initPos = $finalPos+1; $finalPos += 200;
                        $privateData = $this -> getChain($message, $initPos, $finalPos); // Longitud 100
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $privateData;
                        break;
                    }
                    case 127:{ // DE 127 - Private Data
                        $initPos = $finalPos+1; $finalPos += 200;
                        $privateData2 = $this -> getChain($message, $initPos, $finalPos); // Longitud 100
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $privateData2;
                        break;
                    }
                    case 128:{ // DE 128 - Message Authentication Code
                        $initPos = $finalPos+1; $finalPos += 16;
                        $messageAuthenticationCode = $this -> getChain($message, $initPos, $finalPos); // Longitud 8
                        $counter++; $id++;
                        $response[$counter] = new stdClass();
                        $response[$counter] -> $number = $id;
                        $response[$counter] -> $field = $catalog[$i][$field];
                        $response[$counter] -> $name = $catalog[$i][$name];
                        $response[$counter] -> $type = $catalog[$i][$type];
                        $response[$counter] -> $value = $messageAuthenticationCode;
                        break;
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
