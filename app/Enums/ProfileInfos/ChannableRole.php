<?php declare(strict_types=1);

namespace App\Enums\ProfileInfos;

use BenSampo\Enum\Enum;

final class ChannableRole extends Enum
{
    const Email = 1;
    const Telefone = 2;
    #[Description('Rede Social')]
    const Rede_Social = 3;
    const Website = 4;
}
