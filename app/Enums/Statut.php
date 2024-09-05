<?php

namespace App\Enums;

enum Statut: int
{
    case Inactif = 0;
    case EnAttente = 1;
    case Actif = 2;

    public function label(): string
    {
        return match($this) {
            Statut::Inactif => 'inactif',
            Statut::EnAttente => 'en attente',
            Statut::Actif => 'actif',
        };
    }
}
