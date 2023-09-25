@extends('web.layouts.default')

{{-- Stylesheets Section --}}
@section('styles')
@endsection

{{-- Content --}}
@section('content')
    <!-- Content
        ============================================= -->
    <section id="content">
        <div class="content-wrap">
            <div class="section m-0 header-stick footer-stick">
                <div class="container">
                    <div class="heading-block text-center">
                        <h2>
                            {{ $page->title }}
                        </h2>
                        <span class="ls-1">
                            {{ $page->excerpt }}
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-lg-9 mx-auto bg-tertiary rounded-5 p-3 p-md-5">
                            @include('web.layouts.partials._form-alert')

                            <form method="post" action="{{ route('web.leads.contact-us') }}" id="contact-us-form"
                                class="mb-0">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label for="contact-us-name">
                                            Name <small>*</small>
                                        </label>

                                        <input type="text" name="name" id="contact-us-name" class="form-control"
                                            value="{{ old('name') }}">
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label for="contact-us-email">
                                            Email <small>*</small>
                                        </label>

                                        <input type="email" name="email" id="contact-us-email" class="form-control"
                                            value="{{ old('email') }}">
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label for="contact-us-phone">
                                            Telefone para contato <small>*</small>
                                        </label>

                                        <input type="text" name="phone" id="contact-us-phone"
                                            class="form-control kt_phone_ptbr_mask" value="{{ old('phone') }}">
                                    </div>

                                    <div class="col-lg-6 form-group">
                                        <label for="contact-us-subject">
                                            Assunto <small>*</small>
                                        </label>

                                        <select name="subject" id="contact-us-subject" class="form-select">
                                            <option value="">
                                                -- Selecione a opção --
                                            </option>
                                            <option value="Dúvidas" {{ old('subject') == 'Dúvidas' ? 'selected' : '' }}>
                                                Dúvidas
                                            </option>
                                            <option value="Sugestões" {{ old('subject') == 'Sugestões' ? 'selected' : '' }}>
                                                Sugestões
                                            </option>
                                            <option value="Trabalhe Conosco"
                                                {{ old('subject') == 'Trabalhe Conosco' ? 'selected' : '' }}>
                                                Trabalhe Conosco
                                            </option>
                                            <option value="Outros" {{ old('subject') == 'Outros' ? 'selected' : '' }}>
                                                Outros
                                            </option>
                                        </select>
                                    </div>

                                    <div class="w-100"></div>

                                    <div class="col-lg-12 form-group">
                                        <label for="contact-us-subject">
                                            Mensagem <small>*</small>
                                        </label>

                                        <textarea name="message" id="contact-us-message" class="form-control"
                                            rows="6" cols="30">{{ old('message') }}</textarea>
                                    </div>

                                    <div class="col-lg-12 form-group d-none">
                                        <input type="text" name="contact-us-botcheck" id="contact-us-botcheck"
                                            class="form-control">
                                    </div>

                                    <div class="col-lg-12 form-group mb-0">
                                        <button type="submit" id="contact-us-submit"
                                            class="button button-large button-rounded button-color"
                                            data-form-action="submit">
                                            <div class="indicator-label">
                                                <i class="uil uil-navigator"></i>
                                                <span>Envie sua mensagem</span>
                                            </div>
                                            <div class="indicator-progress">
                                                Por Favor, Aguarde...
                                                <span class="align-middle spinner-border spinner-border-sm ms-2"></span>
                                            </div>
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" name="prefix" value="contact-us-">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- #content end -->
@endsection

{{-- Scripts Section --}}
@section('scripts')
    <script async src="{{ mix('js/web/form-validation.bundle.js') }}"></script>
    <script async src="{{ mix('js/web/contact-us.bundle.js') }}"></script>
@endsection
