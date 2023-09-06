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
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=PT+Serif:ital@0;1&display=swap" rel="stylesheet">

	<!-- Stylesheets
    ============================================= -->
    <!--Global Stylesheets Bundle(used by all pages)-->
    @vite(['resources/_web-assets/style.css', 'resources/_web-assets/css/font-icons.css'])

    <!-- Plugins/Components CSS -->
    {{-- Styles injected in pages --}}
    @yield('styles')

    <!--Custom Stylesheets-->
    @vite('resources/_web-assets/css/custom.css')

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Document SEO MetaTags
    ============================================= -->
    {!! SEO::generate() !!}
</head>

<body class="stretched">
	<!-- Document Wrapper
	============================================= -->
	<div id="wrapper">
		<!-- Header
		============================================= -->
		<header id="header" class="full-header transparent-header transparent-header-responsive"
            data-sticky-class="not-dark">
			<div id="header-wrap">
				<div class="container">
					<div class="header-row">

						<!-- Logo
						============================================= -->
						<div id="logo">
							<a href="{{ route('web.pgs.index') }}">
								<img class="logo-default"
                                    srcset="{{ Vite::asset('resources/_web-assets/images/incloud-sistemas-logo-h.png') }}, {{ Vite::asset('resources/_web-assets/images/incloud-sistemas-logo-h@2x.png') }} 2x"
                                    src="{{ Vite::asset('resources/_web-assets/images/incloud-sistemas-logo-h@2x.png') }}"
                                    alt="{{ config('app.name') }}">

								<img class="logo-dark"
                                    srcset="{{ Vite::asset('resources/_web-assets/images/incloud-sistemas-logo-h-dark.png') }}, {{ Vite::asset('resources/_web-assets/images/incloud-sistemas-logo-h-dark@2x.png') }} 2x"
                                    src="{{ Vite::asset('resources/_web-assets/images/incloud-sistemas-logo-h-dark@2x.png') }}"
                                    alt="{{ config('app.name') }}">
							</a>
						</div>
                        <!-- #logo end -->

						<div class="header-misc">
						</div>

						<div class="primary-menu-trigger">
							<button class="cnvs-hamburger" type="button" title="Open Mobile Menu">
								<span class="cnvs-hamburger-box">
                                    <span class="cnvs-hamburger-inner"></span>
                                </span>
							</button>
						</div>

						<!-- Primary Navigation
						============================================= -->
						<nav class="primary-menu with-arrows">
							<ul class="menu-container">
								<li class="menu-item">
									<a class="menu-link" href="{{ route('web.pgs.index') }}">
                                        <div>Home</div>
                                    </a>
								</li>

								<li class="menu-item">
									<a class="menu-link" href="{{ route('web.pgs.about') }}">
                                        <div>Sobre</div>
                                    </a>
								</li>

                                <li class="menu-item">
									<a class="menu-link" href="{{ route('web.pgs.contact-us') }}">
                                        <div>Contato</div>
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
			<div class="container">
				<!-- Footer Widgets
				============================================= -->
				<div class="footer-widgets-wrap">
				</div>
                <!-- .footer-widgets-wrap end -->
			</div>

			<!-- Copyrights
			============================================= -->
			<div id="copyrights">
				<div class="container">
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
    @vite(['resources/_web-assets/js/functions.bundle.js'])
</body>
</html>
