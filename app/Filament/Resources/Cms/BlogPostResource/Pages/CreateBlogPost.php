<?php

namespace App\Filament\Resources\Cms\BlogPostResource\Pages;

use App\Filament\Resources\Cms\BlogPostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }

    protected function afterCreate(): void
    {
        // Force post create
        if (!$this->record->cmsPost) {
            $this->record->cmsPost()->create([]);
        }
    }
}
