<?php

namespace App\Enums;

enum Role: string
{
    case VISITEUR = 'ROLE_VISITEUR';
    case ADMIN = 'ROLE_ADMIN';
    case PRE_ADMIN = 'ROLE_PRE_ADMIN';
    case ECOLE_ADMIN = 'ROLE_ECOLE_ADMIN';
    case COACH = 'ROLE_COACH';

    public static function fromName(string $name): ?self
    {
        return defined(self::class . '::' . $name)
            ? constant(self::class . '::' . $name)
            : null;
    }
}
