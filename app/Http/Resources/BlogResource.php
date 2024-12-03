<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            "title"=>$this->title,
            "image" => $this->image ? asset('/') . 'storage/images/blog/150/' . $this->image : null,
            "short_description"=>$this->short_description,
            "description"=>$this->description,
            "status"=>$this->status,
            "created_by"=>$this->created_by,
            "created_at"=>$this->created_at,
        ];
    }
}
