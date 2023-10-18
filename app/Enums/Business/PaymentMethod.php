<?php declare(strict_types=1);

namespace App\Enums\Business;

use BenSampo\Enum\Enum;

final class PaymentMethod extends Enum
{
    const Dinheiro = 1;
    const Pix = 2;
    const Cheque = 3;
    #[Description('Transferência bancária')]
    const Transferência_bancária = 4;
    #[Description('Cartão de débito')]
    const Cartão_de_débito = 5;
    #[Description('Cartão de crédito')]
    const Cartão_de_crédito = 6;
    #[Description('Boleto bancário')]
    const Boleto_bancário = 7;
    const Crediário = 8;
    #[Description('Link de Pagamento')]
    const Link_de_pagamento = 9;
    #[Description('QR code')]
    const QR_code = 10;
    const Outro = 11;
}
