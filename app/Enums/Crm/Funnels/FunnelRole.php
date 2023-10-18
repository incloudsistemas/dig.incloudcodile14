<?php declare(strict_types=1);

namespace App\Enums\Crm\Funnels;

use BenSampo\Enum\Enum;

final class FunnelRole extends Enum
{
    #[Description('Funis de negócios')]
    const business = 1;
    #[Description('Funis de contatos')]
    const contacts = 2;
}
