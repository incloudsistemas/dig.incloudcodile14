<?php declare(strict_types=1);

namespace App\Enums\Cms;

use BenSampo\Enum\Enum;

final class ProductRole extends Enum
{
    const Produto = 1;
    const Serviço = 2;
    const Curso = 3;

    public static function getSlug(): array
    {
        return [
            self::Produto => 'produto',
            self::Serviço => 'servico',
            self::Curso   => 'curso',
        ];
    }

    public static function getSlugByValue(int $role): string
    {
        $slugs = self::getSlug();
        return $slugs[$role] ?? 'default';
    }

    public static function getSlugByDescription(string $roleDesc): string
    {
        $status = constant("self::$roleDesc");

        if ($status === null) {
            return 'default';
        }

        return self::getSlugByValue($status);
    }
}
