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
            <div class="section m-0 pb-0 header-stick footer-stick">
                <div class="container">
                    <div class="heading-block text-center">
                        <h2>
                            {!! $page->title !!}
                        </h2>
                        <span class="ls-1">
                            {!! $page->excerpt !!}
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
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
