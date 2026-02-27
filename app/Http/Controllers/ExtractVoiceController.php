<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ExtractVoiceRequest;
use App\Models\BrandVoiceProfile;
use App\Services\BrandVoiceExtractor;
use Illuminate\Http\JsonResponse;

class ExtractVoiceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ExtractVoiceRequest $request, BrandVoiceExtractor $extractor): JsonResponse
    {
        // Logic: Extract the voice using the LLM
        $profileData = $extractor->extract($request->validated('samples'));

        // RLS ensures 'workspace_id' is handled correctly or validated via auth
        $profile = BrandVoiceProfile::create([
            'workspace_id' => $request->user()->workspace_id,
            'name' => $request->validated('name'),
            'profile' => $profileData,
        ]);

        return response()->json([
            'message' => 'Brand voice extracted successfully.',
            'data' => $profile
        ], 201);
    }
}
