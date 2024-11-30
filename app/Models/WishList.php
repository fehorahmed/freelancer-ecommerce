<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WishList extends Model
{
    use SoftDeletes;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public static function wishListCheckByProduct($product_id, $user_id)
    {
        $data = WishList::where(['product_id' => $product_id, 'user_id' => $user_id])->first();
        if ($data) {
            return true;
        } else {
            return false;
        }
    }
}
