<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // public function product()
    // {
    //     return $this->hasMany(Product::class);
    // }

    public function children()
    {
        return $this->hasMany(Category::class,'root_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class,'root_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class);
    }
}
