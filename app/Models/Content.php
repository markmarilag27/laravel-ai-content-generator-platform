<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $public_id
 * @property int $workspace_id
 * @property int $brand_voice_profile_id
 * @property string|null $body
 * @property int $tokens_used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\BrandVoiceProfile $brandVoiceProfile
 * @property-read \App\Models\Workspace $workspace
 *
 * @method static \Database\Factories\ContentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereBrandVoiceProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereTokensUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content whereWorkspaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Content extends Model
{
    /** @use HasFactory<\Database\Factories\ContentFactory> */
    use BelongsToWorkspace, HasFactory, HasPublicId, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'workspace_id',
        'brand_voice_profile_id',
        'body',
        'tokens_used',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'workspace_id',
        'brand_voice_profile_id',
        'deleted_at',
    ];

    /**
     **********************************************************
     * Relationships
     **********************************************************
     */
    public function brandVoiceProfile(): BelongsTo
    {
        return $this->belongsTo(BrandVoiceProfile::class);
    }
}
