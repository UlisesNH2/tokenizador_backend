<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        if($isoWord === "ISO"){ //Validación si la palabra 'ISO' viene al principio del mensaje
            //Extraer el header TODO: VALIDACION PENDIENTE
            $header = $this -> getChain($message, 3, 11); 
            //Extraer el type TODO: VALIDACION PENDIENTE
            $type = $this -> getChain($message, 12, 15);
            //Extraer el bitmap principal
            $mainBitmap = $this -> getChain($message, 16, 31);
            //Conversión a binario del bitmap principal
            $binaryBitmap = $this -> getBinary($mainBitmap, 0, 16);

            //Identificación de los campos habilidatos
            for($i = 0; $i < strlen($binaryBitmap); $i++){
                if($binaryBitmap[$i] === '1'){
                    array_push($positions, $i+1);
                }
            }
            for($i = 0; $i < count($positions); $i++){
                switch($positions[$i]){
                    case 1:{ //Secondary bitmap
                        $secondaryBitmap = $this -> getChain($message, 32, 47); //Logitud: 16
                        //Conversión a binario del secondary bitmap
                        $secondaryBMValue = $this -> getBinary($secondaryBitmap, 0, 16);
                        break;
                    }
                    case 2:{//Primary Account Number
                        $initPos = $finalPos+1; $finalPos += 18;  
                        $primAcNumber = $this -> getChain($message, $initPos, $finalPos); //Longitud: 19
                        break;
                    }
                    case 3: { //Processing Code
                        $initPos = $finalPos+1; $finalPos += 6;
                        $processingCode = $this -> getChain($message, $initPos, $finalPos); //Longitud: 6
                        break;
                    }
                    case 4:{ //Amount
                        $amount = $this -> getChain($message, 54, 65); //Longitud: 12
                        break;
                    }
                    case 7: { //Date and Time
                        $dateAndTime = $this -> getChain($message, 66, 75); //Longitud: 10
                        break;
                    }
                    case 11: { //System Trace Audit Number
                        $sysTAN = $this -> getChain($message, 76, 81); //Longitud: 6
                        break;
                    }
                    case 12: { //Local Transaction Time
                        $localTransactionTime = $this -> getChain($message, 82, 87); //Longitud: 6
                        break;
                    }
                    case 13: { //Local Tansaction Date
                        $localTransactionDate = $this -> getChain($message, 88, 91);
                        break;
                    }

                }
            }

            //Se retorna la response
            $response = new stdClass();
            $response -> header = $isoWord.$header;
            $response -> type = $type;
            $response -> bitmap = $mainBitmap.$secondaryBitmap;
            $response -> secundaryBitmapValue = $secondaryBMValue;
            $response -> primaryActNum = $primAcNumber;
            $response -> processingCode = $processingCode;
            $response -> amount = $amount;
            $response -> dateTime = $dateAndTime;
            $response -> systemsTAN = $sysTAN;
            $response -> localTT = $localTransactionTime;
            $response -> localTD = $localTransactionDate;
        }
        return $response;
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
}
