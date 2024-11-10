<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{



    public static function getOrderQuantity($order_id)
    {
        return OrderDetail::where('order_id','=',$order_id)->sum('quantity');
    }
}
