<?php

namespace App\Enums;

enum AbsenceType: string
{
    case Emargement_1 = 'E1';
    case Emargement_2 = 'E2';

    public static function fromName(string $name): ?self
    {
        return defined(self::class . '::' . $name)
            ? constant(self::class . '::' . $name)
            : null;
    }
}
