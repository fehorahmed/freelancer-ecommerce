<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
            "coupon_code" => $this->coupon_code,
            "no_of_usage" => $this->no_of_usage,
            "discount_amount" => $this->discount_amount,
            "discount_percentage" => $this->discount_percentage,
            "start_date" => $this->start_date,
            // "start_time"=>$this->start_time,
            "end_date" => $this->end_date,
            "discountby" => $this->discountby,
            // "end_time"=>$this->end_time,
            "status" => $this->status,
            "visibility" => $this->visibility,
            "products"=>ProductResource::collection(Product::whereIn('id',$this->voucherProducts->pluck('product_id'))->get()),
            "created_by" => $this->created_by,
            "updated_by" => $this->updated_by,

        ];
    }
}
