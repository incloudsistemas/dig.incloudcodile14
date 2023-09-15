<?php

namespace App\Filament\Resources\Cms\PageResource\Pages;

use App\Filament\Resources\Cms\PageResource;
use App\Models\Cms\Page;
use App\Services\Cms\PageService;
use App\Services\Cms\PostService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(
                    function (PageService $service, PostService $postService, Page $page)  {
                        $service->deleteSubpagesWhenDeleted($page);
                        $postService->anonymizeUniqueSlugWhenDeleted($page);
                    }
                ),
        ];
    }

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }
}
