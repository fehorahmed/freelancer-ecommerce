<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{




    public function productPrice(){
        return $this->hasOne(ProductPrice::class,'product_id')->where('status',1);
    }
    public function brand(){
        return $this->belongsTo(Brand::class);
    }
    public function unit(){
        return $this->belongsTo(Unit::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function warranty(){
        return $this->belongsTo(Warranty::class);
    }

}
