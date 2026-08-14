<?php

namespace App\Enums;

enum WorkShift: string
{
    case Morning = 'morning';
    case Afternoon = 'afternoon';
    case Night = 'night';

    public function label(): string
    {
        return match ($this) {
            self::Morning => 'Mañana',
            self::Afternoon => 'Tarde',
            self::Night => 'Noche',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $shift) => [
                'value' => $shift->value,
                'label' => $shift->label(),
            ],
            self::cases()
        );
    }
}