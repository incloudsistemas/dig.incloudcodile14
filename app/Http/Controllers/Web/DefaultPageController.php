<?php

namespace App\Http\Controllers\Web;

use App\Models\Cms\Page;
use Illuminate\Http\Request;

class DefaultPageController extends Controller
{
    public function __construct(protected Page $page)
    {
        parent::__construct($page);
    }

    public function index()
    {
        $page = $this->getPage(slug: 'index');
        $this->generateSEOAttribute(page: $page);

        $sliders = $page->getWebSliders(statuses: $this->getPostStatusByUser())
            ->get();

        return view('web.pages.index', compact('page', 'sliders'));
    }

    public function about()
    {
        $page = null;
        // $page = $this->getPage(slug: 'sobre');
        // $this->generateSEOAttribute($page);

        return view('web.pages.about', compact('page'));
    }

    public function contactUs()
    {
        $page = null;
        // $page = $this->getPage('contato');
        // $this->generateSEOAttribute($page);

        return view('web.pages.contact-us', compact('page'));
    }

    public function rules($slug)
    {
        if (!in_array($slug, ['termos-de-uso', 'politica-de-privacidade'])) {
            abort(404);
        }

        $page = null;
        // $page = $this->getPage($slug);
        // $this->generateSEOAttribute($page);

        return view('web.pages.rules', compact('page'));
    }
}
