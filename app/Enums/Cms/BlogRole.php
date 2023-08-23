<?php declare(strict_types=1);

namespace App\Enums\Cms;

use BenSampo\Enum\Enum;

final class BlogRole extends Enum
{
    const Artigo = 1;
    const Link = 2;
    #[Description('Galeria de Fotos e Vídeos')]
    const Galeria = 3;
    const Vídeo = 4;

    public static function getSlug(): array
    {
        return [
            self::Artigo => 'artigo',
            self::Link => 'link',
            self::Galeria => 'galeria',
            self::Vídeo => 'video',
        ];
    }

    public static function getSlugByValue(int $role): string
    {
        $colors = self::getSlug();
        return $colors[$role] ?? 'default';
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
