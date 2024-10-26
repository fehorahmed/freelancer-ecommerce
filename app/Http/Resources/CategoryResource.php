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
            'root_category_name' => $this->parent->name ?? null,
            'name' => $this->name,
            'url' => $this->url ?? null,
            'description' => $this->description ?? null,
            // 'logo' => $this->logo,
            "logo" => $this->logo ? asset('/') . 'storage/images/categories/logo/150/' . $this->logo : null,
            'horizontal_banner' => $this->horizontal_banner ? asset('/') . 'storage/images/categories/banner/mobile/' . $this->horizontal_banner : null,
            'vertical_banner' => $this->vertical_banner ? asset('/') . 'storage/images/categories/banner/mobile/' . $this->vertical_banner : null,
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'meta_og_description' => $this->meta_og_description,
        ];
    }
}
