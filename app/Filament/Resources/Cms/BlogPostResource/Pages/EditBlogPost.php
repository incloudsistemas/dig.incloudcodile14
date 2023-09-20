<?php

namespace App\Filament\Resources\Cms\BlogPostResource\Pages;

use App\Filament\Resources\Cms\BlogPostResource;
use App\Models\Cms\BlogPost;
use App\Services\Cms\PostService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }
}
