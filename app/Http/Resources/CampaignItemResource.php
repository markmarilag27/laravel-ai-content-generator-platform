<?php

namespace App\Http\Resources;

use App\Models\CampaignItem;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CampaignItem $resource */
        $resource = $this->resource;
        $creditUsed = app(CreditService::class)->calculateTokenCost($resource->tokens_used);

        return [
            'public_id' => $resource->public_id,
            'content_type' => $resource->content_type,
            'topic' => $resource->topic,
            'status' => $resource->status->value,
            'credit_used' => $creditUsed,
            'word_count' => $resource->word_count,
            'content' => $resource->output['content'] ?? '',
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
