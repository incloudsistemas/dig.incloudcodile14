<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\BusinessLeadRequest;
use App\Http\Requests\Web\ContactUsRequest;
use App\Http\Requests\Web\NewsletterSubscriberRequest;
use App\Http\Requests\Web\WorkWithUsRequest;
use App\Services\Web\LeadService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    protected array $mailTo = [
        'contato@paulozanella.com.br',
        'contato@incloudsistemas.com.br'
    ];

    protected ?string $recaptchaSecret;

    public function __construct(protected LeadService $service)
    {
        $this->service = $service;

        $this->recaptchaSecret = config('app.g_recapcha_server');
    }

    public function sendContactUsForm(ContactUsRequest $request)
    {
        $data = $request->all();

        $response = $this->service->create($data, 'contact-us', $this->mailTo, $this->recaptchaSecret);

        $lead = $response['success'] ? $response['data'] : null;

        if ($request->wantsJson()) {
            return response()->json($response);
        }

        if ($lead) {
            session()->flash('response', $response);
            return redirect()->back();
        }

        // If errors...
        return redirect()->back()->withErrors($response['message'])->withInput();
    }

    public function sendWorkWithUsForm(WorkWithUsRequest $request)
    {
        $data = $request->all();

        $response = $this->service->create($data, 'work-with-us', $this->mailTo, $this->recaptchaSecret);

        $lead = $response['success'] ? $response['data'] : null;

        if ($request->wantsJson()) {
            return response()->json($response);
        }

        if ($lead) {
            session()->flash('response', $response);
            return redirect()->back();
        }

        // If errors...
        return redirect()->back()->withErrors($response['message'])->withInput();
    }

    public function sendNewsletterSubscriberForm(NewsletterSubscriberRequest $request)
    {
        $data = $request->all();

        $response = $this->service->create($data, 'newsletter-subscriber', $this->mailTo, $this->recaptchaSecret);

        $lead = $response['success'] ? $response['data'] : null;

        if ($request->wantsJson()) {
            return response()->json($response);
        }

        if ($lead) {
            session()->flash('response', $response);
            return redirect()->back();
        }

        // If errors...
        return redirect()->back()->withErrors($response['message'])->withInput();
    }

    public function sendBusinessLeadForm(BusinessLeadRequest $request)
    {
        $data = $request->all();

        $response = $this->service->create($data, 'business-lead', $this->mailTo, $this->recaptchaSecret);

        $lead = $response['success'] ? $response['data'] : null;

        if ($request->wantsJson()) {
            return response()->json($response);
        }

        if ($lead) {
            session()->flash('response', $response);
            return redirect()->back();
        }

        // If errors...
        return redirect()->back()->withErrors($response['message'])->withInput();
    }
}
