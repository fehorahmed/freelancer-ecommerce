<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{


    const PENDING = 1;
    const PROCESSING = 2;
    const PAID = 3;
    const CASHONDELIVERY = 4;
    const PICKED = 5;
    const SHIPPED = 6;
    const DELIVERED = 7;
    const CANCELED = 8;
    const REFUNDED = 9;
    const REFUNDREQUESTED = 10;

    public static function allOrderStatus()
    {
        $arr = [
            1 => 'Pending',
            2 => 'Processing',
            // 3 => 'Paid',
            4 => 'Cash on Delivery',
            // 5 => 'Picked',
            6 => 'Shipped',
            7 => 'Delivered',
            8 => 'Canceled',
        ];
        return $arr;
    }
    public static function getOrderStatus($orderId)
    {
        $data = OrderStatus::where('order_id', '=', $orderId)->orderBy('id', 'desc')->first();
        return  $data ? self::getOrderStatusByKey($data->status) : '';
    }
    public static function getOrderStatusByKey($key)
    {
        $arr = [
            1 => 'Pending',
            2 => 'Processing',
            // 3 => 'Paid',
            4 => 'Cash on Delivery',
            // 5 => 'Picked',
            6 => 'Shipped',
            7 => 'Delivered',
            8 => 'Canceled',
        ];

        if(isset($arr[$key])){
            return $arr[$key];
        }else{
            return $key;
        }
    }
}
