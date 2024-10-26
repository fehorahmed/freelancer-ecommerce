<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{


    public static function getRegulerPrice($productId, $AttId)
    {
        if ($AttId == "" || $AttId == null) {
            $data = ProductPrice::where('product_id', '=', $productId)->where('status', '=', 1)->max('reguler_price');
        } else {
            $data = ProductPrice::where('product_id', '=', $productId)->where('status', '=', 1)->where('product_attribute_id', '=', $AttId)->max('reguler_price');
        }
        return $data ? $data : 0;
    }

    public static function getSalePrice($productId, $AttId)
    {
        if ($AttId == "" || $AttId == null) {
            $data = ProductPrice::where('product_id', '=', $productId)->where('status', '=', 1)->min('sell_price');
        } else {
            $data = ProductPrice::where('product_id', '=', $productId)->where('status', '=', 1)->where('product_attribute_id', '=', $AttId)->min('sell_price');
        }
        return $data ? $data : 0;
    }
}
