<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Http\Requests\StoreCampaignRequest;
use App\Jobs\DecomposeCampaignJob;
use App\Models\BrandVoiceProfile;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    /**
     * Store a new campaign and dispatch the background orchestration agent.
     */
    public function __invoke(StoreCampaignRequest $request, BrandVoiceProfile $profile): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var Campaign $campaign */
        $campaign = DB::transaction(function () use ($user, $profile, $request) {
            return Campaign::create([
                'workspace_id' => $user->workspace_id,
                'brand_voice_profile_id' => $profile->id,
                'title' => $request->input('title'),
                'deadline' => $request->input('deadline'),
                'brief' => $request->input('brief'),
                'status' => CampaignStatus::Pending,
            ]);
        });

        DecomposeCampaignJob::dispatch($user->id, $user->workspace_id, $campaign->id);

        return response()->json([
            'message' => 'Campaign queued for generation.',
            'data' => [
                'campaign_id' => $campaign->public_id,
                'status' => $campaign->status,
            ],
        ], 202);
    }
}
