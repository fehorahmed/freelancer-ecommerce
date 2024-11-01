<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{


    const PENDING = 1;
    const PROCESSING =2;
    const PAID = 3;
    const CASHONDELIVERY = 4;
    const PICKED = 5;
    const SHIPPED = 6;
    const DELIVERED = 7;
    const CANCELED = 8;
    const REFUNDED = 9;
    const REFUNDREQUESTED = 10;
}
