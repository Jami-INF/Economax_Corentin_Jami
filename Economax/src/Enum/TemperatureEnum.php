<?php

namespace App\Enum;

enum TemperatureEnum : int
{
    case ZERO = 0;
    case FIFTY = 50;
    case HOT = 100;
    case BURNING = 200;

    public function getLabel(): string
    {
        return match ($this) {
            self::ZERO => 'Temperature minimun de 0°',
            self::FIFTY => 'Temperature minimun de 50°',
            self::HOT => 'Temperature minimun Hot (100°)',
            self::BURNING => 'Temperature minimun de 200°',
        };
    }

    public function getValue(): int
    {
        return match ($this) {
            self::ZERO => 0,
            self::FIFTY => 50,
            self::HOT => 100,
            self::BURNING => 200,
        };
    }
}
