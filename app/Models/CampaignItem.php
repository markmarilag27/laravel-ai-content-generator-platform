<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Models\Traits\BelongsToWorkspace;
use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $public_id
 * @property int $workspace_id
 * @property int $campaign_id
 * @property string $content_type
 * @property string|null $output
 * @property CampaignStatus $status
 * @property int $retry_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Campaign $campaign
 * @property-read \App\Models\Workspace $workspace
 *
 * @method static \Database\Factories\CampaignItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem whereOutput($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem whereRetryCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignItem whereWorkspaceId($value)
 *
 * @mixin \Eloquent
 */
class CampaignItem extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignItemFactory> */
    use BelongsToWorkspace, HasFactory, HasPublicId;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'content_type',
        'output',
        'status',
        'retry_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CampaignStatus::class,
        ];
    }

    /**
     **********************************************************
     * Relationships
     **********************************************************
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
