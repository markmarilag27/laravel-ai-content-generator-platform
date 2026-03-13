<?php

namespace App\Http\Resources;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Campaign $resource */
        $resource = $this->resource;
        $total = $resource->campaign_items_count ?? 0;
        $completed = $resource->completed_items_count ?? 0;
        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            'public_id' => $resource->public_id,
            'title' => $resource->title,
            'status' => $resource->status->value,
            'status_class' => $this->getStatusClass($resource->status),
            'deadline' => $resource->deadline,
            'brief' => $resource->brief,
            'progress_percentage' => $progress,
            'campaign_items_count' => $total,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }

    /**
     * Map Enum cases to Tailwind v4 brand classes
     */
    private function getStatusClass(CampaignStatus $status): string
    {
        return match ($status) {
            CampaignStatus::Completed => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400',
            CampaignStatus::Processing => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400',
            CampaignStatus::Pending => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
            CampaignStatus::Failed => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400',
        };
    }
}
