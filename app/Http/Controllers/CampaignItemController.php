<?php

namespace App\Http\Controllers;

use App\Http\Resources\CampaignItemResource;
use App\Models\Campaign;
use App\Models\CampaignItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CampaignItemController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Campaign $campaign): AnonymousResourceCollection
    {
        $resource = CampaignItem::query()
            ->where('campaign_id', $campaign->id)
            ->latest('id')
            ->simplePaginate();

        return CampaignItemResource::collection($resource);
    }
}
