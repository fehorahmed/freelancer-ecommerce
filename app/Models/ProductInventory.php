<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductInventory extends Model
{
    protected $guarded = [];
    const NEW_PRODUCT = 1;
    const UPDATE_PRODUCT = 2;
    // const ORDER = 3;
    // const ORDER_EDIT = 4;
    // const ORDER_CANCEL = 5;
    // const STOCK_IN = 6;
    // const STOCK_OUT = 7;


    public static function getStock($productId, $AttId)
    {
        if($AttId=="" || $AttId== null)
        {
            $data = DB::select('SELECT SUM(stock_in) as stock_in, SUM(stock_out) as stock_out FROM product_inventories WHERE product_id = '.$productId.' and status = 1');
        }
        else {
            $data = DB::select('SELECT SUM(stock_in) as stock_in, SUM(stock_out) as stock_out FROM product_inventories WHERE product_id = '.$productId.' and product_attribute_id = '.$AttId.' and status = 1');
        }
        $stock = end($data)->stock_in - end($data)->stock_out;
        return $stock?$stock:0;
    }
}
