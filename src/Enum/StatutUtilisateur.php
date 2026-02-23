<?php
namespace App\Enum;

enum StatutUtilisateur: string
{
    case ACTIF = 'ACTIF';
    case INACTIF = 'INACTIF';
    case SUSPENDU = 'SUSPENDU';
    case BLOQUE = 'BLOQUE';

    public function label(): string
    {
        return match ($this) {
            self::ACTIF => 'Actif',
            self::INACTIF => 'Inactif',
            self::SUSPENDU => 'Suspendu',
            self::BLOQUE => 'Bloqué',
        };
    }
}