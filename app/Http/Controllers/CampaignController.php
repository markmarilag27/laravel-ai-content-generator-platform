<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Http\Requests\CampaignStoreRequest;
use App\Http\Resources\CampaignResource;
use App\Jobs\DecomposeCampaignJob;
use App\Models\BrandVoiceProfile;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $resource = Campaign::query()
            ->withCount([
                'campaignItems',
                'campaignItems as completed_items_count' => function ($query) {
                    $query->where('status', CampaignStatus::Completed->value);
                },
            ])
            ->latest('id')
            ->simplePaginate();

        return CampaignResource::collection($resource);
    }

    public function store(CampaignStoreRequest $request, BrandVoiceProfile $profile): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var Campaign $resource */
        $resource = DB::transaction(function () use ($user, $profile, $request) {
            return Campaign::create([
                'workspace_id' => $user->workspace_id,
                'brand_voice_profile_id' => $profile->id,
                'title' => $request->input('title'),
                'deadline' => $request->input('deadline'),
                'brief' => $request->input('brief'),
                'status' => CampaignStatus::Pending,
            ]);
        });

        DecomposeCampaignJob::dispatch($user->id, $user->workspace_id, $resource->id);

        return response()->json([
            'message' => 'Campaign queued for generation.',
            'data' => new CampaignResource($resource),
        ], 202);
    }

    public function show(Campaign $campaign): CampaignResource
    {
        $resource = $campaign->loadCount([
            'campaignItems',
            'campaignItems as completed_items_count' => function ($query) {
                $query->where('status', CampaignStatus::Completed->value);
            },
        ]);

        return new CampaignResource($resource);
    }

    public function destroy(Campaign $campaign): Response
    {
        DB::transaction(function () use ($campaign) {
            $campaign->delete();
        });

        return response()->noContent();
    }
}
