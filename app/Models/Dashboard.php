<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;
    protected $table = 'test';
    protected $filable = ['CODIGO_RESPUESTA', 'TIPO', 'KQ2_ID_MEDIO_ACCESO', 'MONTO1'];
}
