<?php

namespace App\Filament\Resources\Crm\Contacts;

use App\Enums\DefaultStatus;
use App\Filament\Resources\Crm\Contacts\RoleResource\Pages;
use App\Filament\Resources\Crm\Contacts\RoleResource\RelationManagers;
use App\Models\Crm\Contacts\Role;
use App\Services\Crm\Contacts\RoleService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Tipo de Contato';

    protected static ?string $pluralModelLabel = 'Tipos de Contatos';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 99;

    protected static ?string $navigationLabel = 'Tipos de Contatos';

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Nome'))
                    ->required()
                    ->minLength(2)
                    ->maxLength(255)
                    ->live(debounce: 1000)
                    ->afterStateUpdated(
                        fn (callable $set, ?string $state): ?string =>
                        $set('slug', Str::slug($state))
                    ),
                Forms\Components\TextInput::make('slug')
                    ->label(__('Slug'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label(__('Status'))
                    ->options(DefaultStatus::asSelectArray())
                    ->default(1)
                    ->required()
                    ->in(DefaultStatus::getValues())
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nome'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(
                        fn (string $state): string =>
                        DefaultStatus::getColorByDescription($state)
                    )
                    ->searchable(
                        query: fn (RoleService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchByStatus(query: $query, search: $search)
                    )
                    ->sortable(
                        query: fn (RoleService $service, Builder $query, string $direction): Builder =>
                        $service->tableSortByStatus(query: $query, direction: $direction)
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Cadastro'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Últ. atualização'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort(column: 'created_at', direction: 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options(DefaultStatus::asSelectArray()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                    ])
                        ->dropdown(false),
                    Tables\Actions\DeleteAction::make(),
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
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name')
                    ->label(__('Nome')),
                Infolists\Components\TextEntry::make('slug')
                    ->label(__('Slug')),
                Infolists\Components\TextEntry::make('display_status')
                    ->label(__('Status')),
                Infolists\Components\TextEntry::make('created_at')
                    ->label(__('Cadastro'))
                    ->dateTime('d/m/Y H:i'),
                Infolists\Components\TextEntry::make('updated_at')
                    ->label(__('Últ. atualização'))
                    ->dateTime('d/m/Y H:i'),
            ])
            ->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}
