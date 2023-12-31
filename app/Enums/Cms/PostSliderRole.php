<?php declare(strict_types=1);

namespace App\Enums\Cms;

use BenSampo\Enum\Enum;

final class PostSliderRole extends Enum
{
    #[Description('Padrão (Imagem)')]
    const Padrão = 1;
    const Vídeo = 2;
    #[Description('Youtube Vídeo')]
    const Youtube = 3;

    public static function getSlug(): array
    {
        return [
            self::Padrão  => 'padrao',
            self::Vídeo   => 'video',
            self::Youtube => 'youtube',
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
