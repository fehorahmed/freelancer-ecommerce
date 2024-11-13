<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{

    public function voucherProducts(){
        return $this->hasMany(VoucherProduct::class)->where('status',1);
    }
}
