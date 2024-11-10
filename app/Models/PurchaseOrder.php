<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{

    public static function maxIn()
    {
        $maxindex = PurchaseOrder::max('max_index');
        $max = $maxindex + 1;
        return $max ? $max : 1;
    }
}
