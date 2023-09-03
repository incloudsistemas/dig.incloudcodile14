<?php

namespace App\Filament\Resources\Cms\RelationManagers;

use App\Services\Cms\PostSubcontentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostSubcontentsTabsRelationManager extends RelationManager
{
    protected int $role = 1; // Tabs/Abas
    protected static ?string $service = PostSubcontentService::class;    

    protected static string $relationship = 'subcontents';

    protected static ?string $title = 'Abas';

    protected static ?string $modelLabel = 'Aba';

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
            return !in_array('tabs', $ownerRecord->settings) ? false : true;
        }

        return true;
    }
}
