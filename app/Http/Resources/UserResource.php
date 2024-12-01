<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "email" => $this->email,
            "phone" => $this->phone,
            "email_verified_at" => $this->email_verified_at,
            "gender" => $this->gender,
            "dob" => $this->dob,
            "picture" => $this->picture ? asset('/') . 'storage/images/user/profile/150/' . $this->picture : null,
            "is_from_apps" => $this->is_from_apps,
            "status" => $this->status,
            "created_at" => $this->created_at,
        ];
        // "image"=> $this->image ? asset('/') . 'storage/images/products/gallery_image/150/' . $this->image : null,
    }
}
