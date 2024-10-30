<?php

namespace App\Http\Resources;

use App\Models\ProductInventory;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MultiProductResource extends JsonResource
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
            "url" => $this->url,
            "sku" => $this->sku,
            "brand_id" => $this->brand_id,
            "brand" => new BrandResource($this->brand),
            "unit_id" => $this->unit_id,
            "unit" => new UnitResource($this->unit),
            "category_id" => $this->category_id,
            "category" => new CategoryResource($this->category),
            "warranty_id" => $this->warranty_id,
            "warranty" => new WarrantyResource($this->warranty),
            "short_description" => $this->short_description,
            "description" => $this->description,
            "image" => $this->image ? asset('/') . 'storage/images/products/profile/150/' . $this->image : null,
            "is_featured" => $this->is_featured,
            "is_apps_only" => $this->is_apps_only,
            "type" => $this->type,
            'stock' => ProductInventory::getStock($this->id,null),
            // "price" => new ProductPriceResource($this->productPrice),
            'regular_price' => ProductPrice::getRegulerPrice($this->id,''),
            'sell_price' => ProductPrice::getSalePrice($this->id,''),
        ];
    }
}
