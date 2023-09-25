<?php

namespace App\Services\Web;

use App\Mail\Web\BusinessLeadFormAlert;
use App\Mail\Web\ContactUsForm;
use App\Mail\Web\NewsletterSubscriberFormAlert;
use App\Mail\Web\WorkWithUsForm;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LeadService extends BaseService
{
    protected array $message = [
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

    public function create(array $data, string $role, array $mailTo, ?string $recaptchaSecret = null)
    {
        DB::beginTransaction();

        try {
            $this->honeyPotCheckBot($data);
            $this->reCaptchaProtection($data, $recaptchaSecret);

            $data = $this->mutateFormDataBeforeCreate(data: $data);

            $customMessages = $data['custom_messages'] ?? array();
            $this->setCustomMessages($customMessages);

            // Search and create contact if not exists
            $this->data = true;

            $this->sendEmail(data: $data, mailTo: $mailTo, role: $role);

            DB::commit();

            return [
                'success'   => true,
                'from'      => 'web',
                'message'   => $this->message['success'],
                'data'      => $this->data,
                'fbq_track' => $data['fbq_track'] ?? null,
            ];
        } catch (\Exception $e) {
            DB::rollback();

            return $this->getErrorException($e);
        }
    }

    protected function honeyPotCheckBot(array $data): void
    {
        $prefix = isset($data['prefix']) ? $data['prefix'] : '';

        $botcheck = isset($data[$prefix . 'botcheck']) ? $data[$prefix . 'botcheck'] : '';

        if (!empty($botcheck)) {
            throw new \Exception('Error. => ' . $this->message['error_bot']);
        }

        return;
    }

    protected function reCaptchaProtection(array $data, ?string $recaptchaSecret): void
    {
        if (isset($data['g_recaptcha_response']) && isset($recaptchaSecret)) {
            $recaptchaData = [
                'secret'   => $recaptchaSecret,
                'response' => $data['g_recaptcha_response']
            ];

            $recapVerify = curl_init();

            curl_setopt($recapVerify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($recapVerify, CURLOPT_POST, true);
            curl_setopt($recapVerify, CURLOPT_POSTFIELDS, http_build_query($recaptchaData));
            curl_setopt($recapVerify, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($recapVerify, CURLOPT_RETURNTRANSFER, true);

            $recapResponse = curl_exec($recapVerify);

            $gResponse = json_decode($recapResponse);

            if ($gResponse->success !== true) {
                throw new \Exception('Error. => ' . $this->message['recaptcha_invalid']);
            }
        }

        $forceRecap = (!empty($data['force_recaptcha']) && $data['force_recaptcha'] !== false) ? true : false;

        if ($forceRecap) {
            if (!isset($data['g_recaptcha_response'])) {
                throw new \Exception('Error. => ' . $this->message['recaptcha_error']);
            }
        }

        return;
    }

    protected function setCustomMessages(array $customMessages): void
    {
        $this->message['success'] = $customMessages['success'] ?? $this->message['success'];
        $this->message['error'] = $customMessages['error'] ?? $this->message['error'];
        $this->message['error_bot'] = $customMessages['error_bot'] ?? $this->message['error_bot'];
        $this->message['error_unexpected'] = $customMessages['error_unexpected'] ?? $this->message['error_unexpected'];
        $this->message['recaptcha_invalid'] = $customMessages['recaptcha_invalid'] ?? $this->message['recaptcha_invalid'];
        $this->message['recaptcha_error'] = $customMessages['recaptcha_error'] ?? $this->message['recaptcha_error'];

        return;
    }

    protected function sendEmail(array $data, array $mailTo, string $role): void
    {
        $mailClass = match ($role) {
            'contact-us' => ContactUsForm::class,
            'work-with-us' => WorkWithUsForm::class,
            'newsletter-subscriber' => NewsletterSubscriberFormAlert::class,
            'business-lead' => BusinessLeadFormAlert::class,
            default => throw new \Exception('Error. => ' . $this->message['error_unexpected']),
        };

        Mail::to($mailTo)->send(new $mailClass($data));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}
