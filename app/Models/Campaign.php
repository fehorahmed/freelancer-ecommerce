<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;

    public function campaignProducts()
    {
        return $this->hasMany(CampaignProduct::class, 'campaign_id', 'id');
    }
}
