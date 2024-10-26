<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource
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
            "product_id" => $this->product_id,
            // "product_attribute_id" => $this->product_attribute_id,
            "reguler_price" => $this->reguler_price,
            "sell_price" => $this->sell_price,
            // "status" => $this->status,
            // "created_by" => $this->created_by,
            // "updated_by" => $this->updated_by,
            // "created_at" => $this->created_at,
            // "updated_at" => $this->updated_at,
        ];
    }
}
