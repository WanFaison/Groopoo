<?php

namespace App\Enums;

enum Etat: string
{
    case Debutant = 'Debutant';
    case Moyen = 'Moyen';
    case Senior = 'Senior';

    public static function fromName(string $name): ?self
    {
        return defined(self::class . '::' . $name)
            ? constant(self::class . '::' . $name)
            : null;
    }
}
