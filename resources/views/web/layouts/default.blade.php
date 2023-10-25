<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">

<head>
    <base href="{{ config('app.url') }}">

    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="copyright"
        content="© {{ date('Y') > '2010' ? '2010 - ' . date('Y') : '2010' }} {{ config('app.name') }} {{ config('app.url') }}">
    <meta name="author" content="InCloud - Marketing Digital e Desenvolvimento Web. https://incloudsistemas.com.br" />

    <!-- CSRF Token -->
    <meta content="{{ csrf_token() }}" name="csrf-token" />

    <!-- Font Imports -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=PT+Serif:ital@0;1&display=swap"
        rel="stylesheet">

    <!-- Stylesheets
    ============================================= -->
    <!--Global Stylesheets Bundle(used by all pages)-->
    <!-- Core Style -->
    <link rel="stylesheet" href="{{ mix('web-build/style.bundle.css') }}">
    <!-- Font Icons -->
    {{-- <link rel="stylesheet" href="{{ mix('web-build/css/font-icons.bundle.css') }}"> --}}
    <link rel="stylesheet" href="{{ mix('web-build/css/font-icons.css') }}">

    <!-- Plugins/Components CSS -->
    {{-- Styles injected in pages --}}
    @yield('styles')

    <!--Custom Stylesheets-->
    <link rel="stylesheet" href="{{ mix('web-build/css/custom.bundle.css') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicons
    ================================================== -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/png" sizes="16x16">
    <link rel="apple-touch-icon" href="{{ asset('web-build/images/apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('web-build/images/apple-touch-icon-72x72.png') }}" sizes="72x72">
    <link rel="apple-touch-icon" href="{{ asset('web-build/images/apple-touch-icon-114x114.png') }}" sizes="114x114">
    <link rel="apple-touch-icon" href="{{ asset('web-build/images/apple-touch-icon-144x144.png') }}" sizes="144x144">

    <!-- Document SEO MetaTags
    ============================================= -->
    {!! SEO::generate() !!}
</head>

<body class="sticky-footer stretched page-transition" data-loader="3" data-loader-color="var(--cnvs-themecolor)"
    data-animation-in="fadeIn" data-speed-in="1500" data-animation-out="fadeOut" data-speed-out="800">
    <!-- Document Wrapper
    ============================================= -->
    <div id="wrapper">
        <!-- Header
        ============================================= -->
        <header id="header" class="transparent-header semi-transparent dark" data-sticky-shrink="false"
            data-sticky-offset="0" data-sticky-shrink-offset="0" data-mobile-sticky="true">
            <div id="header-wrap">
                <div class="container">
                    <div class="header-row justify-content-lg-between">
                        <!-- Logo
                        ============================================= -->
                        <div id="logo" class="col-auto order-lg-2 me-lg-0 px-0">
                            <a href="{{ route('web.pgs.index') }}">
                                <img class="logo-default"
                                    srcset="{{ asset('web-build/images/incloud-logo.png') }}, {{ asset('web-build/images/incloud-logo@2x.png') }} 2x"
                                    src="{{ asset('web-build/images/incloud-logo@2x.png') }}"
                                    alt="{{ config('app.name') }}">

                                <img class="logo-dark"
                                    srcset="{{ asset('web-build/images/incloud-logo-dark.png') }}, {{ asset('web-build/images/incloud-logo-dark@2x.png') }} 2x"
                                    src="{{ asset('web-build/images/incloud-logo-dark@2x.png') }}"
                                    alt="{{ config('app.name') }}">
                            </a>
                        </div>
                        <!-- #logo end -->

                        <div class="header-misc">
                        </div>

                        <div class="primary-menu-trigger">
                            <button class="cnvs-hamburger" type="button" title="Open Mobile Menu">
                                <span class="cnvs-hamburger-box"><span class="cnvs-hamburger-inner"></span></span>
                            </button>
                        </div>

                        <!-- Primary Navigation
                        ============================================= -->
                        <nav class="primary-menu with-arrows col-lg-5 order-lg-1 px-0 style-3 menu-spacing-margin">
                            <ul class="menu-container one-page-menu justify-content-lg-end me-lg-4 mb-0"
                                data-easing="easeInOutExpo" data-speed="1500">
                                <li class="menu-item">
                                    <a class="menu-link" href="#" data-href="#about" data-offset="100">
                                        <div>Sobre Nós</div>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a class="menu-link" href="https://incloud.digital"
                                        target="_blank">
                                        <div>Marketing Digital</div>
                                    </a>
                                </li>
                            </ul>
                        </nav>

                        <nav class="primary-menu col-lg-5 order-lg-3 px-0 style-3 menu-spacing-margin">
                            <ul class="menu-container one-page-menu justify-content-lg-start ms-lg-4"
                                data-easing="easeInOutExpo" data-speed="1500">
                                <li class="menu-item">
                                    <a class="menu-link" href="https://incloudsistemas.com.br/desenvolvimento-web"
                                        target="_blank">
                                        <div>Desenvolvimento Web</div>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a class="menu-link" href="#" data-href="#contact-us" data-offset="100">
                                        <div>Fale Conosco</div>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <!-- #primary-menu end -->
                    </div>
                </div>
            </div>
            <div class="header-wrap-clone"></div>
        </header>
        <!-- #header end -->

        @yield('content')

        <!-- Footer
        ============================================= -->
        <footer id="footer" class="dark border-0">
            <!-- Copyrights
            ============================================= -->
            <div id="copyrights">
                <div class="container">
                    <div class="row col-mb-30">
                        <div class="col-md-6 text-center mx-auto">

                            <img  class="footer-logo mx-auto lazy"
                                data-src="{{ asset('web-build/images/incloud-footer.png') }}"
                                alt="{{ config('app.name') }}" width="150" />

                            <span class="ls-1">
                                Copyrights &copy; {{ date('Y') > '2010' ? '2010 - ' . date('Y') : '2010' }} Todos os
                                direitos reservados por {{ config('app.name') }}.
                            </span>

                            <div class="copyright-links text-smaller ls-1">
                                {{-- <a href="{{ route('web.pgs.rules', 'termos-de-uso') }}">
                                    Termos de Uso
                                </a> / --}}
                                <a href="{{ route('web.pgs.rules', 'politica-de-privacidade') }}">
                                    Política de Privacidade
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #copyrights end -->
        </footer>
        <!-- #footer end -->
    </div>
    <!-- #wrapper end -->

    <!-- Go To Top
    ============================================= -->
    <div id="gotoTop" class="uil uil-angle-up"></div>

    @if ($webSettings['whatsapp'] && $webSettings['whatsapp_link'])
        <!-- Whatsapp Btn
        ============================================= -->
        <a href="{{ $webSettings['whatsapp_link'] }}" target="_blank" class="whatsapp-link">
            <div id="whatsapp-button" class="uil uil-whatsapp infinite animated tada slow" data-bs-container="body"
                data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="left"
                data-bs-content="Fale conosco!"></div>
        </a>
    @endif

    <!-- Javascripts
    ============================================= -->
    <!-- Google Recaptcha -->
    <script async src="https://www.google.com/recaptcha/api.js?render={{ config('app.g_recapcha_site') }}"></script>

    <script src="{{ mix('web-build/js/script.bundle.js') }}"></script>
    <script async src="{{ mix('web-build/js/global-custom.bundle.js') }}"></script>

    {{-- Scripts injected in pages --}}
    @yield('scripts')
</body>

</html>
