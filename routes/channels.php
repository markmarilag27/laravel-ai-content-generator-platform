<?php

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('campaign.{publicId}', function (User $user, string $publicId) {
    $user->setPostgresContext($user->id, $user->workspace_id);

    return Campaign::where('public_id', $publicId)->exists();
});
