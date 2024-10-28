<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "product_id"=>$this->product_id,
            "image"=> $this->image ? asset('/') . 'storage/images/products/gallery_image/150/' . $this->image : null,
            // "status"=>$this->status,
            // "created_by"=>$this->created_by,
            // "updated_by"=>$this->updated_by,
            // "created_at"=>$this->created_at,
            // "updated_at"=>$this->updated_at,
        ];
    }
}