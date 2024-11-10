<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            "name" => $this->name,
            "phone" => $this->phone,
            "description" => $this->description,
            "status" => $this->status,
            "status_text" => $this->status == 1 ? 'Active' : 'Inactive',
            "created_by" => $this->created_by,
            "updated_by" => $this->updated_by,

        ];
    }
}
