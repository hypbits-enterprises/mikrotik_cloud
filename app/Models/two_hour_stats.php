<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class two_hour_stats extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'two_hour_stats';
    public $connection = 'mysql2';
}
