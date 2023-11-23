@extends('web.layouts.default')

{{-- Stylesheets Section --}}
@section('styles')
@endsection

{{-- Content --}}
@section('content')
    <!-- Slider
    ============================================= -->
    <section id="slider" class="slider-element slider-parallax min-vh-100 include-header">
        <div class="slider-inner">
            <div class="row align-items-stretch text-center dark w-100 h-100 mx-0 position-absolute">
                <div class="col-lg-6 px-0 dark videoplay-on-hover">
                    <div class="vertical-middle text-center slider-element-fade mt-lg-5">
                        <div class="container">
                            <h2 class="mb-2 ls-1">
                                {!! $subpages[0]->title !!}
                            </h2>
                            <p class="lead ls-1">
                                <span class="bg-tertiary">
                                    {!! $subpages[0]->excerpt !!}
                                </span>
                            </p>

                            @if ($subpages[0]->cta)
                                <a href="{{ $subpages[0]->cta['url'] }}"
                                    target="{{ $subpages[0]->cta['target'] ?? '_blank' }}"
                                    class="button button-white button-large button-shadow button-shadow-dark border border-width-2 border-dark rounded text-dark h-text-light">
                                    <span>{!! $subpages[0]->cta['call'] ?? 'Saiba mais!' !!}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="video-wrap no-placeholder">
                        <video id="slide-video" class="lazy"
                            data-poster="{{ $subpages[0]->getMedia('image')->first()->getUrl() }}" preload="auto" loop muted
                            playsinline>
                            {{-- <source class="lazy" data-src='{{ asset('web-build/images/marketing-digital.webm') }}'
                                type='video/webm'> --}}
                            <source class="lazy" data-src='{{ $subpages[0]->getMedia('video')->first()->getUrl() }}'
                                type='video/mp4'>
                        </video>
                        <div class="video-overlay" style="background-color: rgba(var(--cnvs-quaternarycolor-rgb), 0.5);">
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 px-0 dark videoplay-on-hover">
                    <div class="vertical-middle text-center slider-element-fade mt-lg-5">
                        <div class="container">
                            <h2 class="mb-2 ls-1">
                                {!! $subpages[1]->title !!}
                            </h2>
                            <p class="lead ls-1">
                                <span class="bg-primary">
                                    {!! $subpages[1]->excerpt !!}
                                </span>
                            </p>

                            @if ($subpages[1]->cta)
                                <a href="{{ $subpages[1]->cta['url'] }}"
                                    target="{{ $subpages[1]->cta['target'] ?? '_blank' }}"
                                    class="button button-white button-large button-shadow button-shadow-dark border border-width-2 border-dark rounded text-dark h-text-light">
                                    <span>{!! $subpages[1]->cta['call'] ?? 'Saiba mais!' !!}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="video-wrap no-placeholder">
                        <video id="slide-video" class="lazy"
                            data-poster="{{ $subpages[1]->getMedia('image')->first()->getUrl() }}" preload="auto" loop muted
                            playsinline>
                            {{-- <source class="lazy" data-src='{{ asset('web-build/images/marketing-digital.webm') }}'
                                type='video/webm'> --}}
                            <source class="lazy" data-src='{{ $subpages[1]->getMedia('video')->first()->getUrl() }}'
                                type='video/mp4'>
                        </video>
                        <div class="video-overlay" style="background-color: rgba(var(--cnvs-secondarycolor-rgb), 0.8);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- #slider end -->

    <!-- Content
    ============================================= -->
    <section id="content">
        <div class="content-wrap">
            <div id="about" class="section dark m-0 header-stick lazy"
                data-bg="{{ asset('web-build/images/incloud-background.jpg') }}"
                style="background-position: center center; background-size: cover; background-repeat: no-repeat;">
                <div class="container z-9">
                    <div class="heading-block text-center">
                        <h2 class="text-uppercase ls-1 px-5">
                            {!! $subpages[2]->subtitle !!}
                        </h2>
                        <span class="ls-1">
                            {!! $subpages[2]->excerpt !!}
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-lg-10 ls-1 mx-auto">
                            {!! $subpages[2]->body !!}
                        </div>
                    </div>
                </div>

                <div class="video-overlay" style="background-color: rgba(var(--cnvs-secondarycolor-rgb), 0.4);">
                </div>
            </div>

            <div id="contact-us" class="section dark m-0 footer-stick lazy"
                data-bg="{{ asset('web-build/images/incloud-digital-background.jpg') }}"
                style="background-position: center center; background-size: cover; background-repeat: no-repeat;">
                <div class="container z-9">
                    <div class="row col-mb-30">
                        <div class="col-lg-6">
                            <div class="heading-block text-start">
                                <h3 class="text-uppercase ls-1">
                                    {!! $subpages[3]->title !!}
                                </h3>
                            </div>

                            <div class="clear mb-0"></div>

                            <div class="feature-box fbox-sm fbox-plain mb-4">
                                <div class="fbox-icon">
                                    <a href="{{ $webSettings['whatsapp_link'] }}" target="_blank">
                                        <i class="bi-whatsapp color-whatsapp"></i>
                                    </a>
                                </div>
                                <div class="fbox-content custom-link" data-href="{{ $webSettings['whatsapp_link'] }}"
                                    data-target="_blank">
                                    <p class="text-smaller text-uppercase ls-1 mt-0">
                                        Whatsapp
                                    </p>
                                    <h3>
                                        {{ $webSettings['whatsapp'] }}
                                    </h3>
                                </div>
                            </div>

                            <div class="feature-box fbox-sm fbox-plain mb-4">
                                <div class="fbox-icon">
                                    <a href="mailto:{{ $webSettings['mail'] }}">
                                        <i class="bi-envelope-at text-muted"></i>
                                    </a>
                                </div>
                                <div class="fbox-content custom-link" data-href="mailto:{{ $webSettings['mail'] }}">
                                    <p class="text-smaller text-uppercase ls-1 mt-0">
                                        Email
                                    </p>
                                    <h3>
                                        {{ $webSettings['mail'] }}
                                    </h3>
                                </div>
                            </div>

                            <div class="feature-box fbox-sm fbox-plain">
                                <div class="fbox-icon">
                                    <a href="{{ $webSettings['gmaps_link'] }}" target="_blank">
                                        <i class="bi-geo-alt text-muted"></i>
                                    </a>
                                </div>
                                <div class="fbox-content custom-link" data-href="{{ $webSettings['gmaps_link'] }}"
                                    data-target="_blank">
                                    <p class="text-smaller text-uppercase ls-1 mt-0">
                                        Onde estamos
                                    </p>
                                    <h3>
                                        {!! $webSettings['address'] !!}
                                    </h3>
                                </div>
                            </div>

                            <div class="clear mb-5"></div>

                            <a href="https://instagram.com/incloud.digital" target="_blank"
                                class="social-icon border-transparent bg-light h-bg-instagram" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="@incloud.digital">
                                <i class="fa-brands fa-instagram"></i>
                                <i class="fa-brands fa-instagram"></i>
                            </a>

                            <a href="{{ $webSettings['instagram_link'] }}" target="_blank"
                                class="social-icon border-transparent bg-light h-bg-instagram" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $webSettings['instagram'] }}">
                                <i class="fa-brands fa-instagram"></i>
                                <i class="fa-brands fa-instagram"></i>
                            </a>

                            <a href="{{ $webSettings['facebook_link'] }}" target="_blank"
                                class="social-icon border-transparent bg-light h-bg-facebook" title="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                        </div>

                        <div class="col-lg-6">
                            <div class="card bg-tertiary border-0">
                                <div class="card-body p-4 p-lg-5">
                                    <h3 class="text-light fw-bolder mb-0">
                                        {!! $subpages[3]->subtitle !!}
                                    </h3>

                                    <p class="text-smaller ls-1">
                                        {!! $subpages[3]->excerpt !!}
                                    </p>

                                    @include('web.layouts.partials._form-alert')

                                    <form method="post" action="{{ route('web.leads.business') }}"
                                        id="business-lead-form" class="mb-0 not-dark">
                                        @csrf

                                        <div class="row">
                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-name" class="text-light">
                                                    <small>*</small> Nome
                                                </label>

                                                <input type="text" name="name" id="business-lead-name"
                                                    class="form-control" value="{{ old('name') }}">
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-email" class="text-light">
                                                    <small>*</small> Email
                                                </label>

                                                <input type="email" name="email" id="business-lead-email"
                                                    class="form-control" value="{{ old('email') }}">
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-phone" class="text-light">
                                                    <small>*</small> Telefone para contato
                                                </label>

                                                <input type="text" name="phone" id="business-lead-phone"
                                                    class="form-control kt_phone_ptbr_mask" value="{{ old('phone') }}">
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-company" class="text-light">
                                                    <small>*</small> Empresa
                                                </label>

                                                <input type="text" name="company" id="business-lead-company"
                                                    class="form-control" value="{{ old('company') }}">
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-company-segment" class="text-light">
                                                    <small>*</small> Segmento de atuação da empresa
                                                </label>

                                                <select name="company_segment" id="business-lead-company-segment"
                                                    class="form-select">
                                                    <option value="">
                                                        -- Selecione a opção --
                                                    </option>
                                                    <option value="Serviços"
                                                        {{ old('company_segment') == 'Serviços' ? 'selected' : '' }}>
                                                        Serviços
                                                    </option>
                                                    <option value="Comércio"
                                                        {{ old('company_segment') == 'Comércio' ? 'selected' : '' }}>
                                                        Comércio
                                                    </option>
                                                    <option value="Indústria"
                                                        {{ old('company_segment') == 'Indústria' ? 'selected' : '' }}>
                                                        Indústria
                                                    </option>
                                                    <option value="Outros"
                                                        {{ old('company_segment') == 'Outros' ? 'selected' : '' }}>
                                                        Outros
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-company-occupation" class="text-light">
                                                    <small>*</small> Cargo
                                                </label>

                                                <select name="company_occupation" id="business-lead-company-occupation"
                                                    class="form-select">
                                                    <option value="">
                                                        -- Selecione a opção --
                                                    </option>
                                                    <option value="Sócio/Dono"
                                                        {{ old('company_occupation') == 'Sócio/Dono' ? 'selected' : '' }}>
                                                        Sócio/Dono
                                                    </option>
                                                    <option value="Diretor"
                                                        {{ old('company_occupation') == 'Diretor' ? 'selected' : '' }}>
                                                        Diretor
                                                    </option>
                                                    <option value="Gerente/Coordenador"
                                                        {{ old('company_occupation') == 'Gerente/Coordenador' ? 'selected' : '' }}>
                                                        Gerente/Coordenador
                                                    </option>
                                                    <option value="Auxiliar/Assistente"
                                                        {{ old('company_occupation') == 'Auxiliar/Assistente' ? 'selected' : '' }}>
                                                        Auxiliar/Assistente
                                                    </option>
                                                    <option value="Consultor"
                                                        {{ old('company_occupation') == 'Consultor' ? 'selected' : '' }}>
                                                        Consultor
                                                    </option>
                                                    <option value="Autônomo"
                                                        {{ old('company_occupation') == 'Autônomo' ? 'selected' : '' }}>
                                                        Autônomo
                                                    </option>
                                                    <option value="Outros"
                                                        {{ old('company_occupation') == 'Outros' ? 'selected' : '' }}>
                                                        Outros
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-company-employees" class="text-light">
                                                    <small>*</small> Nº de funcionários
                                                </label>

                                                <select name="company_employees" id="business-lead-company-employees"
                                                    class="form-select">
                                                    <option value="">
                                                        -- Selecione a opção --
                                                    </option>
                                                    <option value="1-5"
                                                        {{ old('company_employees') == '1-5' ? 'selected' : '' }}>
                                                        1-5
                                                    </option>
                                                    <option value="6-10"
                                                        {{ old('company_employees') == '6-10' ? 'selected' : '' }}>
                                                        6-10
                                                    </option>
                                                    <option value="11-50"
                                                        {{ old('company_employees') == '11-50' ? 'selected' : '' }}>
                                                        11-50
                                                    </option>
                                                    <option value="51-250"
                                                        {{ old('company_employees') == '51-250' ? 'selected' : '' }}>
                                                        51-250
                                                    </option>
                                                    <option value="251-1000"
                                                        {{ old('company_employees') == '251-1000' ? 'selected' : '' }}>
                                                        251-1000
                                                    </option>
                                                    <option value="+1000"
                                                        {{ old('company_employees') == '+1000' ? 'selected' : '' }}>
                                                        +1000
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-company-target" class="text-light">
                                                    <small>*</small> Para quem você vende?
                                                </label>

                                                <select name="company_target" id="business-lead-company-target"
                                                    class="form-select">
                                                    <option value="">
                                                        -- Selecione a opção --
                                                    </option>
                                                    <option value="Para empresas - B2B"
                                                        {{ old('company_target') == 'Para empresas - B2B' ? 'selected' : '' }}>
                                                        Para empresas - B2B
                                                    </option>
                                                    <option value="Direto para o consumidor - B2C"
                                                        {{ old('company_target') == 'Direto para o consumidor - B2C' ? 'selected' : '' }}>
                                                        Direto para o consumidor - B2C
                                                    </option>
                                                    <option value="Ambos - B2B e B2C"
                                                        {{ old('company_target') == 'Ambos - B2B e B2C' ? 'selected' : '' }}>
                                                        Ambos - B2B e B2C
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-company-website" class="text-light">
                                                    Website
                                                </label>

                                                <input type="text" name="company_website"
                                                    placeholder="Caso já tenha um site, informar aqui..."
                                                    id="business-lead-company-website" class="form-control"
                                                    value="{{ old('company_website') }}">
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <label for="business-lead-message" class="text-light">
                                                    Mensagem
                                                </label>

                                                <textarea name="message" id="business-lead-message" class="form-control"
                                                    placeholder="Deseja enviar alguma mensagem adcional?" rows="4" cols="30">{{ old('message') }}</textarea>
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <div class="form-check mb-0">
                                                    <input type="checkbox" id="ruleCheck" class="form-check-input"
                                                        value="1">
                                                    <label class="form-check-label text-light" for="ruleCheck">
                                                        Estou ciente e aceito a <span
                                                            style="text-decoration:underline dotted; text-underline-offset: 0.375rem;"><a
                                                                href="{{ route('web.pgs.rules', 'politica-de-privacidade') }}"
                                                                class="text-white" target="_blank">política de
                                                                privacidade</a></span>.
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-lg-12 form-group d-none">
                                                <input type="text" name="business-lead-botcheck"
                                                    id="business-lead-botcheck" class="form-control">
                                            </div>

                                            <div class="col-lg-12 form-group mb-2">
                                                <button type="submit" id="business-lead-submit"
                                                    class="button button-white button-large button-shadow button-shadow-dark border border-width-2 border-dark rounded text-dark h-text-light m-0"
                                                    data-form-action="submit">
                                                    <div class="indicator-label">
                                                        <i class="uil uil-navigator"></i>
                                                        <span>Envie sua mensagem</span>
                                                    </div>
                                                    <div class="indicator-progress">
                                                        Por Favor, Aguarde...
                                                        <span
                                                            class="align-middle spinner-border spinner-border-sm ms-2"></span>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>

                                        <input type="hidden" class="g-recaptcha-site"
                                            value="{{ config('app.g_recapcha_site') }}">
                                        <input type="hidden" name="g-recaptcha-response" class="g-recaptcha-response"
                                            value="">

                                        <input type="hidden" name="post_id" value="{{ $page->cmsPost->id }}">
                                        <input type="hidden" name="page" value="{{ $page->title ?? $page->name }}">

                                        <input type="hidden" name="prefix" value="business-lead-">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="video-overlay" style="background-color: rgba(var(--cnvs-quaternarycolor-rgb), 0.8);">
                </div>
            </div>
        </div>
    </section>
    <!-- #content end -->
@endsection

{{-- Scripts Section --}}
@section('scripts')
    <script async src="{{ mix('web-build/js/form-validation.bundle.js') }}"></script>
    <script async src="{{ mix('web-build/js/business-lead.bundle.js') }}"></script>
@endsection
