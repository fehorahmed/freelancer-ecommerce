<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    public function division (){
        return $this->belongsTo(Division::class);
    }

    public function subdistricts (){
        return $this->hasMany(SubDistrict::class,'district_id');
    }
}
