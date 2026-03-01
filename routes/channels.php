<?php

use App\Models\Campaign;
use App\Models\User;
use App\Traits\Database\InteractsWithPostgresRls;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('campaign.{publicId}', function (User $user, string $publicId) {
    // 1. Temporarily open RLS for the auth check (since WebSockets hit a different route)
    app(InteractsWithPostgresRls::class)
        ->setPostgresContext($user->id, $user->workspace_id);

    // 2. Try to find the campaign. If RLS blocks it, they don't own it!
    $campaign = Campaign::where('public_id', $publicId)->first();

    return $campaign !== null;
});
