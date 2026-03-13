<?php

namespace App\Http\Resources;

use App\Models\Content;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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

        $creditUsed = app(CreditService::class)->calculateTokenCost($resource->tokens_used);
        $tokenUsedText = "{$creditUsed} ".Str::plural('Credit', $creditUsed);

        return [
            'public_id' => $resource->public_id,
            'body' => $resource->body,
            'tokens_used' => $tokenUsedText,
            'brand_voice_profile' => new BrandVoiceProfileResource($this->whenLoaded('brandVoiceProfile')),
            'workspace' => new WorkspaceResource($this->whenLoaded('workspace')),
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
