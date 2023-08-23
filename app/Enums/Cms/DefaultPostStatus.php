<?php declare(strict_types=1);

namespace App\Enums\Cms;

use BenSampo\Enum\Enum;

final class DefaultPostStatus extends Enum
{
    const Publicado = 1;
    const Rascunho = 2;
    const Inativo = 0;

    public static function getStatusColors(): array
    {
        return [
            self::Publicado => 'success',
            self::Rascunho => 'warning',
            self::Inativo => 'danger',
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
