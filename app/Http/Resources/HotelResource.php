<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'division' => $this->division->name,
            'district' => $this->district->name,
            'sub_district' => $this->subDistrict->name,
            'address' => $this->address,
            'created_by' => $this->createdBy->name??'',
            'status' => $this->status,

        ];
        // return parent::toArray($request);
    }
}
