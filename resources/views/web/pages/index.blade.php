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
            <div class="section m-0 pb-0 header-stick">
                <div class="container">
                    <div class="heading-block text-center">
                        <h2>
                            {{ $page->title }}
                        </h2>
                        <span class="ls-1">
                            {{ $page->excerpt }}
                        </span>
                    </div>

                    <div class="row col-mb-50">
                        <div class="col-lg-6 text-center text-lg-start">
                            <img class="lazy"
                                data-src="{{ asset('images/web/paulo-victor-zanella.jpg') }}"
                                alt="Paulo Zanella" data-animate="fadeInLeft">
                        </div>

                        <div class="col-lg-6">
                            <h4>
                                Vestibulum eleifend ex non quam dignissim.
                            </h4>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec sapien tincidunt,
                                feugiat mauris sit amet, dapibus felis. Nulla tempus est nulla, eu ullamcorper velit
                                imperdiet sit amet. Quisque vel purus auctor, egestas mi nec, rhoncus turpis. Nulla non
                                felis quis orci condimentum bibendum.
                            </p>
                            <p>
                                Donec porta felis nec fringilla ornare. Suspendisse placerat leo dictum semper interdum.
                                Mauris gravida a ligula euismod varius. Nunc laoreet tincidunt justo sed imperdiet. Aenean
                                consequat tincidunt elit. Duis eget tempor tortor.
                            </p>
                            <p>
                                Morbi sit amet iaculis est. Duis in lectus a dolor pretium lacinia. Aliquam nisl enim,
                                scelerisque vitae pellentesque a, hendrerit faucibus lorem. Sed fermentum posuere dolor, sit
                                amet molestie metus feugiat id. Ut iaculis tortor accumsan odio commodo eleifend. Nulla non
                                justo nec urna luctus dignissim.
                            </p>
                        </div>
                    </div>
                </div>

                <div id="idx-carousel" class="owl-carousel image-carousel carousel-widget mt-3 mt-md-6" data-margin="2"
                    data-nav="true" data-pagi="false" data-loop="true" data-autoplay="5000" data-stage-padding="90"
                    data-slideby="2" data-items-xs="1" data-items-sm="1" data-items-md="2" data-items-lg="3"
                    data-lazyload="true" data-animate="fadeInUp">
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/321680825_640491554535062_5798178862851451767_n.jpeg') }}"
                            alt="Image 1">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/321718859_878952523288783_8954911764277244172_n.jpeg') }}"
                            alt="Image 2">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/321868566_210572051346545_4647306259367905979_n.jpeg') }}"
                            alt="Image 3">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/321920888_198234449403023_3495137717605438560_n.jpeg') }}"
                            alt="Image 4">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/322062716_472491731739621_1346951323628680603_n.jpeg') }}"
                            alt="Image 5">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/347072255_796757251654058_6804174911090529059_n.jpeg') }}"
                            alt="Image 6">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/347228101_191212936809275_289295329964123891_n.jpeg') }}"
                            alt="Image 7">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/347262894_270167588787752_251521177650855994_n.jpeg') }}"
                            alt="Image 8">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/347442689_1583007722195390_949735712889745891_n.jpeg') }}"
                            alt="Image 9">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/360011363_1220078232016337_8158792128970414407_n.jpeg') }}"
                            alt="Image 10">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/360043154_820522012806661_7439337900957890774_n.jpeg') }}"
                            alt="Image 11">
                    </div>
                    <div class="oc-item">
                        <img class="lazy"
                            data-src="{{ asset('images/web/gallery/360059900_812076853765497_4880657281702302364_n.jpeg') }}"
                            alt="Image 12">
                    </div>
                </div>
            </div>

            <div class="section bg-tertiary m-0 footer-stick">
                <div class="container">
                    <div class="heading-block text-center">
                        <h4>Clientes</h4>
                        <span class="ls-1">Veja algumas das marcas que já passaram pelo ateliê</span>
                    </div>

                    <ul class="clients-grid row row-cols-2 row-cols-sm-3 row-cols-md-5 mb-0"
                        data-animate="fadeInUp">
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/1.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/2.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/3.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/4.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/5.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/6.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/7.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/8.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/9.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                        <li class="grid-item">
                            <a href="#">
                                <img class="bg-color lazy"
                                    data-src="{{ asset('images/web/clients/10.png') }}"
                                    alt="Clients">
                            </a>
                        </li>
                    </ul>

                    <div class="owl-carousel carousel-widget mt-6" data-nav="false" data-pagi="true" data-loop="true"
                        data-autoplay="5000" data-items-xs="1" data-items-sm="1" data-items-md="2" data-items-lg="3"
                        data-lazyload="true" data-animate="fadeInUp">
                        <div class="oc-item">
							<div class="row flex-row-reverse g-2">
								<div class="col">
									<div class="quote-bubble quote-bubble-left mb-3 bg-secondary text-white text-smaller ls-1">
										<p>Seamlessly conceptualize multimedia based web services for optimal human capital. Collaboratively evisculate e-business value.</p>
									</div>
									<h4 class="ps-3 h6 mb-0 fw-medium">John Doe</h4>
									<small class="ps-3 text-muted">Google Inc.</small>
								</div>
								<div class="col-auto">
									<img class="rounded-circle mt-1 lazy"
                                        data-src="{{ asset('images/web/testimonials/1.jpg') }}"
                                        alt="Customer Testimonails" width="48">
								</div>
							</div>
						</div>
                        <div class="oc-item">
							<div class="row flex-row-reverse g-2">
								<div class="col">
									<div class="quote-bubble quote-bubble-left mb-3 bg-dark text-white text-smaller ls-1">
										<p>Seamlessly conceptualize multimedia based web services for optimal human capital. Collaboratively evisculate e-business value.</p>
									</div>
									<h4 class="ps-3 h6 mb-0 fw-medium">John Doe</h4>
									<small class="ps-3 text-muted">Google Inc.</small>
								</div>
								<div class="col-auto">
									<img class="rounded-circle mt-1 lazy"
                                        data-src="{{ asset('images/web/testimonials/2.jpg') }}"
                                        alt="Customer Testimonails" width="48">
								</div>
							</div>
						</div>
                        <div class="oc-item">
							<div class="row flex-row-reverse g-2">
								<div class="col">
									<div class="quote-bubble quote-bubble-left mb-3 bg-secondary text-white text-smaller ls-1">
										<p>Seamlessly conceptualize multimedia based web services for optimal human capital. Collaboratively evisculate e-business value.</p>
									</div>
									<h4 class="ps-3 h6 mb-0 fw-medium">John Doe</h4>
									<small class="ps-3 text-muted">Google Inc.</small>
								</div>
								<div class="col-auto">
									<img class="rounded-circle mt-1 lazy"
                                        data-src="{{ asset('images/web/testimonials/3.jpg') }}"
                                        alt="Customer Testimonails" width="48">
								</div>
							</div>
						</div>
                        <div class="oc-item">
							<div class="row flex-row-reverse g-2">
								<div class="col">
									<div class="quote-bubble quote-bubble-left mb-3 bg-dark text-white text-smaller ls-1">
										<p>Seamlessly conceptualize multimedia based web services for optimal human capital. Collaboratively evisculate e-business value.</p>
									</div>
									<h4 class="ps-3 h6 mb-0 fw-medium">John Doe</h4>
									<small class="ps-3 text-muted">Google Inc.</small>
								</div>
								<div class="col-auto">
									<img class="rounded-circle mt-1 lazy"
                                        data-src="{{ asset('images/web/testimonials/7.jpg') }}"
                                        alt="Customer Testimonails" width="48">
								</div>
							</div>
						</div>
                        <div class="oc-item">
							<div class="row flex-row-reverse g-2">
								<div class="col">
									<div class="quote-bubble quote-bubble-left mb-3 bg-secondary text-white text-smaller ls-1">
										<p>Seamlessly conceptualize multimedia based web services for optimal human capital. Collaboratively evisculate e-business value.</p>
									</div>
									<h4 class="ps-3 h6 mb-0 fw-medium">John Doe</h4>
									<small class="ps-3 text-muted">Google Inc.</small>
								</div>
								<div class="col-auto">
									<img class="rounded-circle mt-1 lazy"
                                        data-src="{{ asset('images/web/testimonials/5.jpg') }}"
                                        alt="Customer Testimonails" width="48">
								</div>
							</div>
						</div>
                        <div class="oc-item">
							<div class="row flex-row-reverse g-2">
								<div class="col">
									<div class="quote-bubble quote-bubble-left mb-3 bg-dark text-white text-smaller ls-1">
										<p>Seamlessly conceptualize multimedia based web services for optimal human capital. Collaboratively evisculate e-business value.</p>
									</div>
									<h4 class="ps-3 h6 mb-0 fw-medium">John Doe</h4>
									<small class="ps-3 text-muted">Google Inc.</small>
								</div>
								<div class="col-auto">
									<img class="rounded-circle mt-1 lazy"
                                        data-src="{{ asset('images/web/testimonials/8.jpg') }}"
                                        alt="Customer Testimonails" width="48">
								</div>
							</div>
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
@endsection
