<?php declare(strict_types=1);

namespace App\Enums\ProfileInfos;

use BenSampo\Enum\Enum;

final class Uf extends Enum
{
    const Acre = 'AC';
    const Alagoas = 'AL';
    const Amapá = 'AP';
    const Amazonas = 'AM';
    const Bahia = 'BA';
    const Ceará = 'CE';
    #[Description('Distrito Federal')]
    const Distrito_Federal = 'DF';
    #[Description('Espírito Santo')]
    const Espírito_Santo = 'ES';
    const Goiás = 'GO';
    const Maranhão = 'MA';
    #[Description('Mato Grosso')]
    const Mato_Grosso = 'MT';
    #[Description('Mato Grosso do Sul')]
    const Mato_Grosso_do_Sul = 'MS';
    #[Description('Minas Gerais')]
    const Minas_Gerais = 'MG';
    const Pará = 'PA';
    const Paraíba = 'PB';
    const Paraná = 'PR';
    const Pernambuco = 'PE';
    const Piauí = 'PI';
    #[Description('Rio de Janeiro')]
    const Rio_de_Janeiro = 'RJ';
    #[Description('Rio Grande do Norte')]
    const Rio_Grande_do_Norte = 'RN';
    #[Description('Rio Grande do Sul')]
    const Rio_Grande_do_Sul = 'RS';
    const Rondônia = 'RO';
    const Roraima = 'RR';
    #[Description('Santa Catarina')]
    const Santa_Catarina = 'SC';
    #[Description('São Paulo')]
    const São_Paulo = 'SP';
    const Sergipe = 'SE';
    const Tocantins = 'TO';
}
