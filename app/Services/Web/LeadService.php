<?php

namespace App\Services\Web;

class LeadService extends BaseService
{
    private array $message = [
        'success'           => 'Recebemos com sucesso o seu cadastro e entraremos em contato contigo assim que possível.',
        'error'             => 'O cadastro não pôde ser efetuado devido a algum erro inesperado. Por favor, tente novamente mais tarde.',
        'error_bot'         => 'Bot detectado! O formulário não pôde ser processado! Por favor, tente novamente!',
        'error_unexpected'  => 'Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.',
        'recaptcha_invalid' => 'Captcha não validado! Por favor, tente novamente!',
        'recaptcha_error'   => 'Captcha não enviado! Por favor, tente novamente.',
    ];

    public function __construct()
    {
        //
    }
}
