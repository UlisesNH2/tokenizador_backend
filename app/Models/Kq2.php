<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kq2 extends Model
{
    use HasFactory;
    //protected $table = 'test';
    public $ID;
    public $TX_Description;
    public $TX_Accepted;
    public $TX_Rejected;
    public $accepted_Amount;
    public $rejected_Amount;
    public $percenTX_Accepted;
    public $percenTX_Rejected;
}
