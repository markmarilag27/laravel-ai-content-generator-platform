<?php

use App\Models\Campaign;
use App\Models\User;
use App\Traits\Database\InteractsWithPostgresRls;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('campaign.{publicId}', function (User $user, string $publicId) {
    $rls = app(InteractsWithPostgresRls::class);

    $rls->setPostgresContext($user->id, $user->workspace_id);

    return Campaign::where('public_id', $publicId)->exists();
});
