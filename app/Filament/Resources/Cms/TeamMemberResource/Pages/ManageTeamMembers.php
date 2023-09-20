<?php

namespace App\Filament\Resources\Cms\TeamMemberResource\Pages;

use App\Filament\Resources\Cms\TeamMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTeamMembers extends ManageRecords
{
    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
