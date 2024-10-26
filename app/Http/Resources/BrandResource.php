<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            "name" => $this->name,
            "description" => $this->description ?? null,
            "url" => $this->url ?? null,
            "logo" => $this->logo ? asset('/') . 'storage/images/brands/logo/150/' . $this->logo : null,
            "horizontal_banner" => $this->horizontal_banner ? asset('/') . 'storage/images/brands/banner/mobile/' . $this->horizontal_banner : null, $this->horizontal_banner,
            "vertical_banner" => $this->vertical_banner ? asset('/') . 'storage/images/brands/banner/mobile/' . $this->vertical_banner : null,
            "status" => $this->status,
            "created_by" => $this->created_by,
            "updated_by" => $this->updated_by ?? null,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
        // return parent::toArray($request);
    }
}
