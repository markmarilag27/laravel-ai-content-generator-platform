<?php

namespace App\Http\Resources;

use App\Models\User;
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
        /** @var User $resource */
        $resource = $this->resource;

        return [
            'public_id' => $resource->public_id,
            'name' => $resource->name,
            'email' => $resource->email,
            'email_verified_at' => $resource->email_verified_at,
            'workspace' => new WorkspaceResource($this->whenLoaded('workspace')),
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
