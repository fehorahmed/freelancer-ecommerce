<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            "image" => $this->image ? asset('/') . 'storage/images/sliders/' . $this->image : null,
            "image_1600" => $this->image ? asset('/') . 'storage/images/sliders/1600/' . $this->image : null,
            "serial"=>$this->serial,
        ];
    }
}
