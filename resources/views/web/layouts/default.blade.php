<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">

<head>
    <base href="{{ config('app.url') }}">

    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="copyright"
        content="© {{ date('Y') > '2023' ? '2023 - ' . date('Y') : '2023' }} {{ config('app.name') }} {{ config('app.url') }}">
    <meta name="author" content="InCloud - Marketing Digital e Desenvolvimento Web. https://incloudsistemas.com.br" />

    <!-- CSRF Token -->
    <meta content="{{ csrf_token() }}" name="csrf-token" />

    <!-- Font Imports -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Montserrat:wght@400;700&family=Merriweather&display=swap"
        rel="stylesheet">

    <!-- Stylesheets
    ============================================= -->
    <!--Global Stylesheets Bundle(used by all pages)-->
    <!-- Core Style -->
	<link rel="stylesheet" href="{{ mix('css/web/style.bundle.css') }}">
    <!-- Font Icons -->
    <link rel="stylesheet" href="{{ mix('css/web/font-icons.bundle.css') }}">

    <!-- Plugins/Components CSS -->
    {{-- Styles injected in pages --}}
    @yield('styles')

    <!--Custom Stylesheets-->
    <link rel="stylesheet" href="{{ mix('css/web/custom.bundle.css') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicons
    ================================================== -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/png" sizes="16x16">
    <link rel="apple-touch-icon" href="{{ asset('images/web/apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/web/apple-touch-icon-72x72.png') }}" sizes="72x72" >
    <link rel="apple-touch-icon" href="{{ asset('images/web/apple-touch-icon-114x114.png') }}" sizes="114x114" >
    <link rel="apple-touch-icon" href="{{ asset('images/web/apple-touch-icon-144x144.png') }}" sizes="144x144" >

    <!-- Document SEO MetaTags
    ============================================= -->
    {!! SEO::generate() !!}
</head>

<body class="sticky-footer stretched page-transition" data-loader="5" data-loader-color="var(--cnvs-themecolor)"
    data-animation-in="fadeIn" data-speed-in="1500" data-animation-out="fadeOut" data-speed-out="800">
    <!-- Document Wrapper
    ============================================= -->
    <div id="wrapper">
        <!-- Header transparent-header dark
        ============================================= -->
        <header id="header" class="header-size-lg no-sticky" data-sticky-class="not-dark"
            data-responsive-class="not-dark" data-sticky-logo-height="80" data-sticky-menu-padding="29">
            <div id="header-wrap">
                <div class="container">
                    <div class="header-row justify-content-lg-between">
                        <!-- Logo
                        ============================================= -->
                        <div id="logo" class="col-auto order-lg-2 me-lg-0 px-0">
                            <a href="{{ route('web.pgs.index') }}">
                                <img class="logo-default"
                                    srcset="{{ asset('images/web/paulo-victor-zanella-atelie-fotografico.png') }}, {{ asset('images/web/paulo-victor-zanella-atelie-fotografico@2x.png') }} 2x"
                                    src="{{ asset('images/web/paulo-victor-zanella-atelie-fotografico@2x.png') }}"
                                    alt="{{ config('app.name') }}">

                                <img class="logo-dark"
                                    srcset="{{ asset('images/web/paulo-victor-zanella-atelie-fotografico-dark.png') }}, {{ asset('images/web/paulo-victor-zanella-atelie-fotografico-dark@2x.png') }} 2x"
                                    src="{{ asset('images/web/paulo-victor-zanella-atelie-fotografico-dark@2x.png') }}"
                                    alt="{{ config('app.name') }}">
                            </a>
                        </div>
                        <!-- #logo end -->

                        <div class="primary-menu-trigger">
                            <button class="cnvs-hamburger" type="button" title="Open Mobile Menu">
                                <span class="cnvs-hamburger-box"><span class="cnvs-hamburger-inner"></span></span>
                            </button>
                        </div>

                        <!-- Primary Navigation
                        ============================================= -->
                        <nav class="primary-menu with-arrows col-lg-5 order-lg-1 px-0 style-3 menu-spacing-margin">
                            <ul class="menu-container">
                                <li class="menu-item {{ $page->slug == 'index' ? 'current' : '' }}">
                                    <a class="menu-link" href="{{ route('web.pgs.index') }}">
                                        <div>Sobre</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ $page->slug == 'fotos' ? 'current' : '' }}">
                                    <a class="menu-link" href="{{ route('web.pgs.photos') }}">
                                        <div>Fotos</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ $page->slug == 'videos' ? 'current' : '' }}">
                                    <a class="menu-link" href="{{ route('web.pgs.videos') }}">
                                        <div>Vídeos</div>
                                    </a>
                                </li>
                            </ul>
                        </nav>

                        <nav class="primary-menu col-lg-5 order-lg-3 px-0 style-3 menu-spacing-margin">
                            <ul class="menu-container justify-content-lg-end">
                                <li class="menu-item {{ $page->slug == 'blog' ? 'current' : '' }}">
                                    <a class="menu-link" href="{{ route('web.blog.index') }}">
                                        <div>Blog</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ $page->slug == 'fale-conosco' ? 'current' : '' }}">
                                    <a class="menu-link" href="{{ route('web.pgs.contact-us') }}">
                                        <div>Fale Conosco</div>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="#" target="_blank"
                                        class="social-icon border-transparent si-small h-bg-instagram" title="Instagram">
                                        <i class="fa-brands fa-instagram"></i>
                                        <i class="fa-brands fa-instagram"></i>
                                    </a>
                                    <a href="#" target="_blank"
                                        class="social-icon border-transparent si-small h-bg-youtube" title="Youtube">
                                        <i class="fa-brands fa-youtube"></i>
                                        <i class="fa-brands fa-youtube"></i>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?phone=5562991388707" target="_blank"
                                        class="social-icon border-transparent si-small h-bg-whatsapp" title="Whatsapp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        <i class="fa-brands fa-whatsapp"></i>
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
        <footer id="footer" class="dark">
            <!-- Copyrights
            ============================================= -->
            <div id="copyrights">
                <div class="container">
                    <div class="row col-mb-30">
                        <div class="col-md-6 text-center text-md-start">
                            <div class="copyrights-menu copyright-links">
                                <a href="{{ route('web.pgs.index') }}">
                                    Sobre
                                </a>/
                                <a href="{{ route('web.pgs.photos') }}">
                                    Fotos
                                </a>/
                                <a href="{{ route('web.pgs.videos') }}">
                                    Vídeos
                                </a>/
                                <a href="{{ route('web.blog.index') }}">
                                    Blog
                                </a>/
                                <a href="{{ route('web.pgs.contact-us') }}">
                                    Fale Conosco
                                </a>
                            </div>

                            <span class="">
                                Copyrights &copy; {{ date('Y') > '2023' ? '2023 - ' . date('Y') : '2023' }} Todos os
                                direitos reservados por {{ config('app.name') }}.
                            </span>

                            <div class="copyright-links text-smaller ls-1">
                                <a href="{{ route('web.pgs.rules', 'termos-de-uso') }}">
                                    Termos de Uso
                                </a> /
                                <a href="{{ route('web.pgs.rules', 'politica-de-privacidade') }}">
                                    Política de Privacidade
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6 text-center text-md-end">
                            <div class="d-flex justify-content-center justify-content-md-end mb-2">
                                <a href="#" class="social-icon border-transparent si-small h-bg-instagram">
                                    <i class="fa-brands fa-instagram"></i>
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                                <a href="#" class="social-icon border-transparent si-small h-bg-facebook">
                                    <i class="fa-brands fa-facebook-f"></i>
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-icon border-transparent si-small h-bg-youtube">
                                    <i class="fa-brands fa-youtube"></i>
                                    <i class="fa-brands fa-youtube"></i>
                                </a>
                                <a href="#" class="social-icon border-transparent si-small me-0 h-bg-linkedin">
                                    <i class="fa-brands fa-linkedin"></i>
                                    <i class="fa-brands fa-linkedin"></i>
                                </a>
                                <a href="https://api.whatsapp.com/send?phone=5562991388707" target="_blank"
                                    class="social-icon border-transparent si-small me-0 h-bg-whatsapp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>

                            <i class="bi-envelope"></i> contato@paulozanella.com.br
                            <span class="middot">&middot;</span>
                            <i class="fa-solid fa-phone"></i> +55 (62) 99138-8707
                            <br/>
                            <img class="lazy mt-2"
                                data-src="{{ asset('images/web/desenvolvido-por-incloud-dark.png') }}"
                                alt="Desenvolvido por InCloud Sistemas" title="Desenvolvido por InCloud Sistemas" />
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

    <!-- Javascripts
    ============================================= -->
    <script src="{{ mix('js/web/script.bundle.js') }}"></script>
    <script async src="{{ mix('js/web/global-custom.bundle.js') }}"></script>

    <!-- Plugins/Components JS -->
    {{-- Scripts injected in pages --}}
    @yield('scripts')
</body>

</html>
