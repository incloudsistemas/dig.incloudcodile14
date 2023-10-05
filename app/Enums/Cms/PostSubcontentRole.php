<?php declare(strict_types=1);

namespace App\Enums\Cms;

use BenSampo\Enum\Enum;

final class PostSubcontentRole extends Enum
{
    const Abas = 1;
    const Acordeões = 2;

    public static function getSlug(): array
    {
        return [
            self::Abas      => 'abas',
            self::Acordeões => 'acordeoes',
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
