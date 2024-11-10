<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
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
            "date"=>$this->date,
            "supplier_id"=>$this->supplier_id,
            "supplier"=> new SupplierResource($this->supplier),
            "max_index"=>$this->max_index,
            "order_no"=>$this->order_no,
            "sub_total_amount"=>$this->sub_total_amount,
            "grand_total_amount"=>$this->grand_total_amount,
            "shipping_charge"=>$this->shipping_charge,
            "vat"=>$this->vat,
            "discount"=>$this->discount,
            "paid"=>$this->paid,
            "due"=>$this->due,
            "order_detail"=> PurchaseOrderDetailResource::collection($this->orderDetail),
            "created_by"=>$this->created_by,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
    }
}
