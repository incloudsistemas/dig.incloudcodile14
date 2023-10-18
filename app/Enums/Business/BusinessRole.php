<?php declare(strict_types=1);

namespace App\Enums\Business;

use BenSampo\Enum\Enum;

final class BusinessRole extends Enum
{
    #[Description('[CRM] Padrão')]
    const crm_default = 1;
    #[Description('[Loja] Ponto de Venda')]
    const shop_point_of_sale = 2;
    #[Description('[Loja] Loja Virtual')]
    const shop_e_commerce = 3;
}
