<?php

namespace App\Http\Controllers\Web;

use App\Models\Cms\Page;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(protected Page $page)
    {
        $this->page = $page;

        $webSettings =  [
            'mail'           => 'contato@incloudsistemas.com.br',
            'phone'          => null,
            'phone_link'     => null,
            'whatsapp'       => '(62) 98193-6169',
            'whatsapp_link'  => 'https://wa.me/5562981936169',
            'facebook'       => null,
            'facebook_link'  => 'https://www.facebook.com/incloudsistemas',
            'instagram'      => null,
            'instagram_link' => 'https://www.instagram.com/incloud.digital',
            'twitter'        => null,
            'twitter_link'   => null,
            'linkedin'       => null,
            'linkedin_link'  => null,
            'youtube'        => null,
            'youtube_link'   => null,
            'gmaps_link'     => 'https://goo.gl/maps/TVudpDggzXHiJUZA7',
            'address'        => 'Rua 9 de Julho, 1385, Vila São José, Anápolis - GO, 75155-525',
            'coordinates'    => null,
        ];

        View::share('webSettings', $webSettings);

        // $agent = new Agent();

        // View::share('agent', $agent);
    }

    protected function getPage(string $slug): Model
    {
        return $this->page->findWebBySlug(slug: $slug, statuses: $this->getPostStatusByUser())
            ->firstOrFail();
    }

    protected function getPostStatusByUser(): array
    {
        // Verify that the user is logged
        // If yes, show posts with status = 1 - Ativo and 2 - Rascunho
        if (auth()->guard('web')->check()) {
            return [1, 2];
        }

        // If not, show only posts with status 1 - Ativo
        return [1];
    }

    protected function generateSEOAttribute(Model $page, string $type = 'website'): void
    {
        $title = $page->meta_title ?? $page->title ?? $page->name ?? config('app.name', 'InCloud digital');
        SEOTools::setTitle(strip_tags($title));

        $description = $page->meta_description ?? $page->excerpt ?? $page->subtitle ?? $title;
        SEOTools::setDescription(strip_tags($description));

        SEOTools::opengraph()->setUrl(URL::current());

        SEOTools::setCanonical(URL::current());

        SEOTools::opengraph()->addProperty('type', $type);

        if (isset($this->twitter) && !empty($this->twitter)) {
            SEOTools::twitter()->setSite($this->twitter);
        }

        $image = $page->cover_image ? $page->getCoverImageThumb(300, 300) : asset('images/cover.jpg');
        SEOTools::opengraph()->addImage($image);
    }
}
