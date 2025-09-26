<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class one_minute_stats extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'one_minute_stats';
    public $connection = 'mysql2';
}
