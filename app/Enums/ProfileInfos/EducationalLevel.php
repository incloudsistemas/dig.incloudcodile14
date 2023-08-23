<?php declare(strict_types=1);

namespace App\Enums\ProfileInfos;

use BenSampo\Enum\Enum;

final class EducationalLevel extends Enum
{
    const Fundamental = 1;
    const Médio = 2;
    const Superior = 3;
    #[Description('Pós graduação')]
    const Pós_graduação = 4;
    const Mestrado = 5;
    const Doutorado = 6;
}
