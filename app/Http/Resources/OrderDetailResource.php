<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            "order_id" => $this->order_id,
            "product_id" => $this->product_id,
            "product_name" => $this->product->name,
            "image" => $this->product->image ? asset('/') . 'storage/images/products/profile/150/' . $this->product->image : null,
            "attribute_id" => $this->attribute_id,
            "quantity" => $this->quantity,
            "discount_type" => $this->discount_type,
            "discount_amount" => $this->discount_amount,
            "vat" => $this->vat,
            "product_price" => $this->product_price,
            "net_price" => $this->net_price,
            "warranty_id" => $this->warranty_id,
        ];
    }
}
