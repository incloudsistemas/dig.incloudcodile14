<?php

namespace App\Filament\Resources\Cms\RelationManagers;

use App\Enums\Cms\DefaultPostStatus;
use App\Models\Cms\PostSubcontent;
use App\Services\Cms\PostSubcontentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PostSubcontentsAccordionsRelationManager extends RelationManager
{
    protected int $role = 2; // Accordions/Acordeões
    protected static ?string $service = PostSubcontentService::class;    

    protected static string $relationship = 'subcontents';

    protected static ?string $title = 'Acordeões';

    protected static ?string $modelLabel = 'Acordeão';

    public function form(Form $form): Form
    {
        return static::$service::getForm(form: $form, role: $this->role);
    }

    public function table(Table $table): Table
    {
        return static::$service::getTable(table: $table, role: $this->role);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return static::$service::getInfolist(infolist: $infolist);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if ($ownerRecord->getTable() === 'cms_pages') {
            return !in_array('accordions', $ownerRecord->settings) ? false : true;
        }

        return true;
    }
}
