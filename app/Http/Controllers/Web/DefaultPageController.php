<?php

namespace App\Http\Controllers\Web;

use App\Models\Cms\Page;
use App\Models\Cms\Partner;
use App\Models\Cms\Testimonial;
use Illuminate\Http\Request;

class DefaultPageController extends Controller
{
    public function __construct(protected Page $page, protected Partner $partner, protected Testimonial $testimonial)
    {
        parent::__construct($page);

        $this->partner = $partner;
        $this->testimonial = $testimonial;
    }

    public function index(): \Illuminate\View\View
    {
        $page = $this->getPage(slug: 'index');
        $this->generateSEOAttribute(page: $page);

        $subpages = $page->subpages()
            ->get();

        $partners = $this->partner->getWebFeatured(statuses: $this->getPostStatusByUser())
            ->get();

        $testimonials = $this->testimonial->getWebFeatured(statuses: $this->getPostStatusByUser())
            ->get();

        return view('web.pages.index', compact('page', 'subpages', 'partners', 'testimonials'));
    }

    public function about(): \Illuminate\View\View
    {
        $page = $this->getPage('sobre');
        $this->generateSEOAttribute($page);

        return view('web.pages.about', compact('page'));
    }

    public function contactUs(): \Illuminate\View\View
    {
        $page = $this->getPage('fale-conosco');
        $this->generateSEOAttribute($page);

        return view('web.pages.contact-us', compact('page'));
    }

    public function rules($slug): \Illuminate\View\View
    {
        if (!in_array($slug, ['termos-de-uso', 'politica-de-privacidade'])) {
            abort(404);
        }

        $page = $this->getPage($slug);
        $this->generateSEOAttribute($page);

        return view('web.pages.rules', compact('page'));
    }
}
