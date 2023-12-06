<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvertAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_url' => $this->image_url,
            'url' => $this->url,
            'position' => $this->position,
            'impressions' => $this->impressions,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
