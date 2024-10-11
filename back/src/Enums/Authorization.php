<?php

namespace App\Enums;

enum Authorization: string
{
    case Admin = 'admin';
    case EcoleAdmin = 'ecole admin';
    case Visiteur = 'visiteur';
}
