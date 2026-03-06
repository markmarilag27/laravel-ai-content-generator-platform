<?php

namespace App\Http\Resources;

use App\Models\BrandVoiceProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandVoiceProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var BrandVoiceProfile $resource */
        $resource = $this->resource;

        return [
            'public_id' => $resource->public_id,
            'name' => $resource->name,
            'samples' => $resource->samples,
            'profile' => $resource->profile,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
