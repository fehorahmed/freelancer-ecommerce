<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
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
            "user_id"=>$this->user_id,
            "user"=>new UserResource($this->user),
            "review"=>$this->review,
            "star"=>$this->star,
            "status"=>$this->status,
            "created_at"=>$this->created_at,
        ];
    }
}
