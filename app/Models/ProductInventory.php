<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    protected $guarded = [];
    const NEW_PRODUCT = 1;
    const UPDATE_PRODUCT = 2;
    const ORDER = 3;
    const ORDER_EDIT = 4;
    const ORDER_CANCEL = 5;
    const STOCK_IN = 6;
    const STOCK_OUT = 7;
}
