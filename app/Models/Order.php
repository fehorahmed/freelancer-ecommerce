<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{


    public static function maxIn()
    {
        $maxindex=Order::max('max_index');
        $max = $maxindex + 1;
       return $max?$max:1;
    }
}