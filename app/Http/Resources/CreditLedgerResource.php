<?php

namespace App\Http\Resources;

use App\Models\CreditLedger;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditLedgerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CreditLedger $resource */
        $resource = $this->resource;

        return [
            'public_id' => $resource->public_id,
            'amount' => $resource->amount,
            'type' => $resource->type,
            'description' => $resource->description,
            'metadata' => $resource->metadata,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
