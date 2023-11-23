<?php declare(strict_types=1);

namespace App\Enums\Crm\Contacts;

use BenSampo\Enum\Enum;

final class TaxRegime extends Enum
{
    const MEI = 1;
    #[Description('L. Presumido')]
    const Lucro_Presumido = 2;
    #[Description('L. Real')]
    const Lucro_Real = 3;
    #[Description('L. Arbitrado')]
    const Lucro_Arbitrado = 4;
    const Física = 5;
}
