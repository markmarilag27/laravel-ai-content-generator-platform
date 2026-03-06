<?php

namespace App\Http\Resources;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Workspace $resource */
        $resource = $this->resource;

        return [
            'public_id' => $resource->public_id,
            'name' => $resource->name,
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'credits_remaining' => $resource->credits_remaining,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
