<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasPublicId
{
    /**
     * Perform any actions required after the model boots.
     */
    protected static function bootHasPublicId(): void
    {
        static::creating(function (Model $model): void {
            if (! $model->getAttribute('public_id')) {
                $model->forceFill([
                    'public_id' => (string) Str::uuid(),
                ]);
            }
        });
    }

    /**
     * Get the value of the model's route key.
     */
    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
}
