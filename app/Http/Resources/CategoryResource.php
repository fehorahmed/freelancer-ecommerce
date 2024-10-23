<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'root_id' => $this->root_id,
            'root_category_name' => $this->parent->name??'',
            'name' => $this->name,
            'url' => $this->url ,
            'description' => $this->description ,
            'logo' => $this->logo,
            'horizontal_banner' => $this->horizontal_banner,
            'vertical_banner' => $this->vertical_banner,
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'meta_og_description' => $this->meta_og_description,
        ];
    }
}
