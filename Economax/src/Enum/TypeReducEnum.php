<?php

namespace App\Enum;

enum TypeReducEnum : string
{
    case PERCENT = 'percent';
    case AMOUNT = 'amount';

    public function getLabel(): string
    {
        return match ($this) {
            self::PERCENT => 'Pourcentage',
            self::AMOUNT => 'Montant',
        };
    }
}