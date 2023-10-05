<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserStatus extends Enum
{
    const Ativo = 1;
    const Pendente = 2;
    const Inativo = 0;

    public static function getStatusColors(): array
    {
        return [
            self::Ativo    => 'success',
            self::Pendente => 'warning',
            self::Inativo  => 'danger',
        ];
    }

    public static function getColorByValue(int $status): string
    {
        $colors = self::getStatusColors();
        return $colors[$status] ?? 'default';
    }

    public static function getColorByDescription(string $statusDesc): string
    {
        $status = constant("self::$statusDesc");

        if ($status === null) {
            return 'default';
        }

        return self::getColorByValue($status);
    }
}
