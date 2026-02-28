<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GenerateContentRequest;
use App\Models\BrandVoiceProfile;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BrandVoiceGenerator;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GenerateContentController extends Controller
{
    public function __construct(
        protected BrandVoiceGenerator $generator,
        protected CreditService $credits
    ) {}

    /**
     * Handle the generation request.
     */
    public function __invoke(GenerateContentRequest $request, BrandVoiceProfile $profile): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var Workspace $workspace */
        $workspace = $user->workspace;

        // Run the Generation Engine (Recursive retries happen here)
        $output = $this->generator->generate($profile, $request->validated(), $workspace);

        // Deduct on success
        $this->credits->deduct(
            $workspace,
            $output['tokens'],
            'generation',
            json_encode($output),
            [
                'tokens' => $output['tokens'],
                'model' => 'gpt-4o',
                'attempts' => $output['attempts'],
            ]
        );

        return response()->json([
            'message' => 'Content generated successfully.',
            'content' => $output,
            'remaining_balance' => $this->credits->getBalance($workspace),
        ]);
    }
}
