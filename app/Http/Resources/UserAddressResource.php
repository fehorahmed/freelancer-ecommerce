<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
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
            // "user_id" => $this->user_id,
            "name" => $this->name,
            "phone" => $this->phone,
            "alt_phone" => $this->alt_phone,
            "sub_district_id" => $this->sub_district_id,
            "sub_district_name" => $this->subDistrict->name,
            "district_id" => $this->subDistrict->district_id,
            "district_name" => $this->subDistrict->district->name,
            "divission_id" => $this->subDistrict->district->division_id,
            "divission_name" => $this->subDistrict->district->division->name,
            "address" => $this->address,

        ];
    }
}
