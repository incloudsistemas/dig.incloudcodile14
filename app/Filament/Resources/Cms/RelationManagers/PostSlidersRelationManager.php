<?php

namespace App\Filament\Resources\Cms\RelationManagers;

use App\Services\Cms\PostSliderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostSlidersRelationManager extends RelationManager
{
    protected static ?string $service = PostSliderService::class;

    protected static bool $shouldSkipAuthorization = true;

    protected static string $relationship = 'sliders';

    protected static ?string $title = 'Sliders';

    protected static ?string $modelLabel = 'Slider';

    public function form(Form $form): Form
    {
        return static::$service::getForm(form: $form);
    }

    public function table(Table $table): Table
    {
        $idxPg = false;
        if ($this->ownerRecord->getTable() === 'cms_pages' && $this->ownerRecord->id === 1) {
            $idxPg = true;
        }

        return static::$service::getTable(table: $table, idxPg: $idxPg)
            ->recordTitleAttribute('title')
            ->headerActions([
                Tables\Actions\CreateAction::make()->hidden(
                    fn (): bool =>
                    $idxPg && !auth()->user()->can('Excluir [Cms] Sliders')
                ),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return static::$service::getInfolist(infolist: $infolist);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if ($ownerRecord->getTable() === 'cms_pages') {
            // Index page
            if ($ownerRecord->id === 1 && !auth()->user()->can('Visualizar [Cms] Sliders')) {
                return false;
            }

            return !in_array('sliders', $ownerRecord->settings) ? false : true;
        }

        return true;
    }
}
