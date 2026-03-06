<?php

namespace App\Http\Resources;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Content $resource */
        $resource = $this->resource;

        return [
            'public_id' => $resource->public_id,
            'body' => $resource->body,
            'tokens_used' => $resource->tokens_used,
            'brand_voice_profile' => new BrandVoiceProfileResource($this->whenLoaded('brandVoiceProfile')),
            'workspace' => new WorkspaceResource($this->whenLoaded('workspace')),
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
