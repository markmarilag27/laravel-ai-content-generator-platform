<?php

namespace App\Enums;

enum PlanName: string
{
    case Free = 'free';
    case Pro = 'pro';
    case Enterprise = 'enterprise';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
