<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{


    const CASH_ON_DELIVERY = 1;
    const BKASH = 2;
    const NAGAD = 3;
    const SSL_COMMERZ = 4;
    const FAILED = 5;
    const REFUNDED = 6;
    const CANCELED = 7;
    const UPAY = 8;
}
