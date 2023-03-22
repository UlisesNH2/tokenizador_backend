<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class BnetController extends Controller{

    public function bnetMessage(Request $request){
        $message = $request->message;
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
