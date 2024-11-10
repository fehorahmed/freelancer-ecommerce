<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderDetailResource extends JsonResource
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
            "purchase_order_id"=>$this->purchase_order_id,
            "product_id"=>$this->product_id,
            "product"=>new ProductResource($this->product),
            "attribute_id"=>$this->attribute_id,
            "rate"=>$this->rate,
            "quantity"=>$this->quantity,
            "total_amount"=>$this->total_amount,
            "discount_type"=>$this->discount_type,
            "discount_amount"=>$this->discount_amount,
            "vat"=>$this->vat,
            "deleted_at"=>$this->deleted_at,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
    }
}
