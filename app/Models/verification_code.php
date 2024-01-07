<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class verification_code extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $connection = "mysql2";
}
