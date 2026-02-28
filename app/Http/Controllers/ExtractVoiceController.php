<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ExtractVoiceRequest;
use App\Models\BrandVoiceProfile;
use App\Models\User;
use App\Services\BrandVoiceExtractor;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ExtractVoiceController extends Controller
{
    public function __construct(
        protected CreditService $credits
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(ExtractVoiceRequest $request, BrandVoiceExtractor $extractor): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Logic: Extract the voice (returns ['profile' => [...], 'tokens' => int])
        $result = $extractor->extract($request->validated('samples'));

        // Persistence: Create the profile within the user's workspace
        $profile = BrandVoiceProfile::query()->create([
            'workspace_id' => $user->workspace_id,
            'name' => $request->validated('name'),
            'profile' => $result['profile'],
        ]);

        // Audit: Log the token usage in the credit ledger
        // We charge 0 credits for extraction (it's part of setup), but track tokens.
        $this->credits->recordEntry(
            $user->workspace_id,
            0,
            'voice_extraction',
            "Profile created: {$profile->name}",
            ['tokens' => $result['tokens']]
        );

        return response()->json([
            'message' => 'Brand voice extracted successfully.',
            'data' => $profile,
        ], 201);
    }
}
