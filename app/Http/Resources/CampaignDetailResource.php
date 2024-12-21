<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "banner" => $this->banner,
            "banner" => $this->banner ? asset('/') . 'storage/images/campaign/banner/mobile/' . $this->logo : null,
            "description" => $this->description,
            "url" => $this->url,
            "start_date" => $this->start_date,
            "start_time" => $this->start_time,
            "end_date" => $this->end_date,
            "end_time" => $this->end_time,
            "status" => $this->status,
            "is_apps_only" => $this->is_apps_only,
            "created_by" => $this->created_by,
            "updated_by" => $this->updated_by,
            "campain_products" => CampaignProductResource::collection($this->campaignProducts),
        ];
    }
}
