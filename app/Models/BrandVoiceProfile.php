<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $public_id
 * @property int $workspace_id
 * @property string $name
 * @property array<array-key, mixed> $profile
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Workspace $workspace
 *
 * @method static \Database\Factories\BrandVoiceProfileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile whereProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile whereWorkspaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BrandVoiceProfile withoutTrashed()
 *
 * @mixin \Eloquent
 */
class BrandVoiceProfile extends Model
{
    /** @use HasFactory<\Database\Factories\BrandVoiceProfileFactory> */
    use BelongsToWorkspace, HasFactory, HasPublicId, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'profile',
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
            'profile' => 'array',
        ];
    }
}
