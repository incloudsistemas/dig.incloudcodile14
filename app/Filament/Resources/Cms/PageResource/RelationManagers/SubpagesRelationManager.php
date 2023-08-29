<?php

namespace App\Filament\Resources\Cms\PageResource\RelationManagers;

use App\Enums\Cms\DefaultPostStatus;
use App\Filament\Resources\Cms\PageResource;
use App\Filament\Resources\Cms\PageResource\Pages\EditPage;
use App\Models\Cms\Page;
use App\Services\Cms\PageService;
use App\Services\Cms\PostService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubpagesRelationManager extends RelationManager
{
    protected static string $relationship = 'subpages';

    protected static ?string $title = 'Lista de Subpáginas';

    protected static ?string $modelLabel = 'Subpágina';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns(PageResource::getTableColumns())
            ->filters(PageResource::getTableFilters())
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label(__('Novo Subpágina'))
                    ->url(
                        fn (): string =>
                        route('filament.i2c-admin.resources.cms.pages.create', ['main-page' => $this->ownerRecord->id])
                    )
                    ->hidden(
                        fn (): bool =>
                        !auth()->user()->can('Cadastrar [Cms] Páginas')
                    )
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        // Tables\Actions\EditAction::make(),
                        Tables\Actions\Action::make('edit')
                            ->label(__('Editar'))
                            ->icon('heroicon-m-pencil-square')
                            ->url(
                                fn (Page $page): string =>
                                route('filament.i2c-admin.resources.cms.pages.edit', ['record' => $page->id])
                            ),
                    ])
                        ->dropdown(false),
                    Tables\Actions\DeleteAction::make()
                        ->after(
                            fn (PageService $service, Page $page) =>
                            $service->anonymizeUniqueSlugWhenDeleted($page)
                        ),
                ])
                    ->label(__('Ações'))
                    ->icon('heroicon-m-chevron-down')
                    ->size(Support\Enums\ActionSize::ExtraSmall)
                    ->color('gray')
                    ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label(__('Novo Subpágina'))
                    ->url(
                        fn (): string =>
                        route('filament.i2c-admin.resources.cms.pages.create', ['main-page' => $this->ownerRecord->id])
                    ),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return PageResource::infolist(infolist: $infolist);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return (!$ownerRecord->subpages->count() > 0 && !auth()->user()->can('Cadastrar [Cms] Páginas')) || $ownerRecord->mainPage
            ? false
            : true;
    }
}
