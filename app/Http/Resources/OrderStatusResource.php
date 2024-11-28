<?php

namespace App\Http\Resources;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusResource extends JsonResource
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
            "status" =>OrderStatus::getOrderStatusByKey($this->status),
            "status_id" => $this->status,
            "remarks" => $this->remarks,
            "date" => $this->date,
        ];
    }
}
