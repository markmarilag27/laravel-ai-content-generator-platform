<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ExtractVoiceStoreRequest;
use App\Http\Requests\ExtractVoiceUpdateRequest;
use App\Http\Resources\BrandVoiceProfileResource;
use App\Models\BrandVoiceProfile;
use App\Models\User;
use App\Services\BrandVoiceExtractor;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrandVoiceProfileController extends Controller
{
    public function __construct(
        protected CreditService $credits,
        protected BrandVoiceExtractor $extractor
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $resource = BrandVoiceProfile::query()->latest('id')->simplePaginate();

        return BrandVoiceProfileResource::collection($resource);
    }

    public function store(ExtractVoiceStoreRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $result = $this->extractor->extract($request->validated('samples'));

        /** @var BrandVoiceProfile $resource */
        $resource = DB::transaction(function () use ($user, $result, $request) {
            return BrandVoiceProfile::query()->create([
                'workspace_id' => $user->workspace_id,
                'name' => $request->validated('name'),
                'samples' => $request->validated('samples'),
                'profile' => $result['profile'],
            ]);
        });

        $this->credits->recordEntry(
            $user->workspace_id,
            0,
            'voice_extraction',
            "Profile created: {$resource->name}",
            ['tokens' => $result['tokens']]
        );

        return response()->json([
            'message' => 'Brand voice extracted successfully.',
            'data' => new BrandVoiceProfileResource($resource),
        ]);
    }

    public function show(Request $request, BrandVoiceProfile $profile): BrandVoiceProfileResource
    {
        return new BrandVoiceProfileResource($profile);
    }

    public function update(ExtractVoiceUpdateRequest $request, BrandVoiceProfile $profile)
    {
        /** @var User $user */
        $user = Auth::user();

        $result = $this->extractor->extract($request->validated('samples'));

        $resource = DB::transaction(function () use ($result, $request, $profile) {
            $profile->update([
                'name' => $request->validated('name'),
                'samples' => $request->validated('samples'),
                'profile' => $result['profile'],
            ]);

            return $profile;
        });

        $this->credits->recordEntry(
            $user->workspace_id,
            0,
            'voice_extraction',
            "Profile updated: {$resource->name}",
            ['tokens' => $result['tokens']]
        );

        return response()->json([
            'message' => 'Brand voice extracted successfully.',
            'data' => new BrandVoiceProfileResource($resource),
        ]);
    }

    public function destroy(BrandVoiceProfile $profile): Response
    {
        DB::transaction(function () use ($profile) {
            $profile->delete();
        });

        return response()->noContent();
    }
}
