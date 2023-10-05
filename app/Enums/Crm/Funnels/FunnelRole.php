<?php declare(strict_types=1);

namespace App\Enums\Crm\Funnels;

use BenSampo\Enum\Enum;

final class FunnelRole extends Enum
{
    #[Description('Funis de negócios')]
    const Business = 1;
    #[Description('Funis de contatos')]
    const Contact = 2;

    public static function getSlug(): array
    {
        return [
            self::Business => 'artigo',
            self::Contact  => 'link',
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
