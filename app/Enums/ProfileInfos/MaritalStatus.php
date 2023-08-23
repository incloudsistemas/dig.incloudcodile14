<?php declare(strict_types=1);

namespace App\Enums\ProfileInfos;

use BenSampo\Enum\Enum;

final class MaritalStatus extends Enum
{
    #[Description('Solteiro(a)')]
    const Solteiro = 1;
    #[Description('Casado(a)')]
    const Casado = 2;
    #[Description('Divorciado(a)')]
    const Divorciado = 3;
    #[Description('Viúvo(a)')]
    const Viúvo = 4;
    #[Description('Separado(a)')]
    const Separado = 5;
    #[Description('Companheiro(a)')]
    const Companheiro = 6;
}
