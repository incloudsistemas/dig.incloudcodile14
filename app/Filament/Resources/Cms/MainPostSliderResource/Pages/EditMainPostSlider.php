<?php

namespace App\Filament\Resources\Cms\MainPostSliderResource\Pages;

use App\Filament\Resources\Cms\MainPostSliderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMainPostSlider extends EditRecord
{
    protected static string $resource = MainPostSliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
