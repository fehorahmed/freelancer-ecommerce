<?php

namespace App\Http\Resources;

use App\Models\OrderDetail;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderHistoryResource extends JsonResource
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
            "user_id" => $this->user_id,
            "max_index" => $this->max_index,
            "order_no" => $this->order_no,
            "quantity" => OrderDetail::getOrderQuantity($this->id),
            "total_amount" => $this->total_amount,
            "shipping_charge" => $this->shipping_charge,
            "vat" => $this->vat,
            "discount" => $this->discount,
            "coupon_code" => $this->coupon_code,
            "user_address" => new UserAddressResource(UserAddress::find($this->user_address_id)),
            "created_by" => $this->created_by,
            "created_at" => $this->created_at,
        ];
    }
}
