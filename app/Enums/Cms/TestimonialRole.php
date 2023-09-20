<?php declare(strict_types=1);

namespace App\Enums\Cms;

use BenSampo\Enum\Enum;

final class TestimonialRole extends Enum
{
    const Texto = 1;
    const Imagem = 2;
    const Vídeo = 3;

    public static function getSlug(): array
    {
        return [
            self::Texto  => 'texto',
            self::Imagem => 'imagem',
            self::Vídeo  => 'video',
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
