<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductResource extends JsonResource
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
            "product" => new ProductResource(Product::find($this->product_id)),

            "campaign_id" => $this->campaign_id,
            // "product_attribute_id" => $this->product_attribute_id,
            // "reguler_price" => $this->reguler_price,
            "sell_price" => $this->sell_price,
            // "start_date" => $this->start_date,
            // "end_date" => $this->end_date,
            // "start_time" => $this->start_time,
            // "end_time" => $this->end_time,
            // "warranty_id"=>$this->warranty_id,
            // "status" => $this->status,
            // "created_by" => $this->created_by,
            // "updated_by" => $this->updated_by,

        ];
    }
}
