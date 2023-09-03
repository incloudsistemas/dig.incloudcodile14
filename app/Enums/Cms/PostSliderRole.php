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
}
