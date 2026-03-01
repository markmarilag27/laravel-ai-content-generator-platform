<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $campaignPublicId,
        public array $statusCounts,
        public int $percentageComplete
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Broadcast on a private channel scoped to the campaign
        return [
            new PrivateChannel("campaign.{$this->campaignPublicId}"),
        ];
    }

    /**
     * The data to broadcast to the frontend.
     */
    public function broadcastWith(): array
    {
        return [
            'public_id' => $this->campaignPublicId,
            'status_counts' => $this->statusCounts,
            'percentage_complete' => $this->percentageComplete,
        ];
    }
}
