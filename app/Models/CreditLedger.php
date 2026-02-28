<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $public_id
 * @property int $workspace_id
 * @property int $amount
 * @property string $type
 * @property string|null $description
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\CreditLedgerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereReferenceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLedger whereWorkspaceId($value)
 *
 * @mixin \Eloquent
 */
class CreditLedger extends Model
{
    /** @use HasFactory<\Database\Factories\CreditLedgerFactory> */
    use HasFactory, HasPublicId;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'workspace_id',
        'amount',
        'type',
        'description',
        'metadata',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'iworkspace_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }
}
