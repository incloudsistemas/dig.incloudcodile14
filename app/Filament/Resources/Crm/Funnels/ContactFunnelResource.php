<?php

namespace App\Filament\Resources\Crm\Funnels;

use App\Enums\DefaultStatus;
use App\Filament\Resources\Crm\Funnels\ContactFunnelResource\Pages;
use App\Filament\Resources\Crm\Funnels\ContactFunnelResource\RelationManagers;
use App\Models\Crm\Funnels\ContactFunnel;
use App\Models\Crm\Funnels\Funnel;
use App\Services\Crm\Funnels\FunnelService;
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

class ContactFunnelResource extends Resource
{
    protected static ?string $model = ContactFunnel::class;

    protected static ?int $role = 2; // 2 - Funis de contatos

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Funil de Contato';

    protected static ?string $pluralModelLabel = 'Funis de Contatos';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 96;

    protected static ?string $navigationLabel = 'Funis de Contatos';

    protected static ?string $navigationIcon = 'heroicon-o-funnel';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Infos. Gerais'))
                    ->description(__('Visão geral e informações fundamentais sobre o funil.'))
                    ->schema([
                        Forms\Components\Hidden::make('role')
                            ->default(static::$role)
                            ->visibleOn('create'),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Nome do funil'))
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Descrição'))
                            ->rows(4)
                            ->minLength(2)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options(DefaultStatus::asSelectArray())
                            ->default(1)
                            ->required()
                            ->in(DefaultStatus::getValues())
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make(__('Estágios do Funil'))
                    ->description(__('Mapeie, organize e otimize cada estágio do seu funil.'))
                    ->schema([
                        Forms\Components\Repeater::make('stages')
                            ->label('')
                            ->relationship(
                                name: 'stages',
                                modifyQueryUsing: fn (FunnelService $service, Builder $query): Builder =>
                                $service->ignoreClosingStages($query)
                            )
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Estágio do funil'))
                                    ->required()
                                    ->minLength(2)
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->itemLabel(
                                fn (array $state): ?string =>
                                $state['name'] ?? null
                            )
                            ->addActionLabel(__('Adicionar estágio'))
                            ->defaultItems(1)
                            ->reorderable(true)
                            ->reorderableWithButtons()
                            ->orderColumn('order')
                            ->collapsible()
                            ->collapseAllAction(
                                fn (Forms\Components\Actions\Action $action) =>
                                $action->label(__('Minimizar todos'))
                            )
                            ->deleteAction(
                                fn (Forms\Components\Actions\Action $action) =>
                                $action->requiresConfirmation()
                            )
                            ->columnSpanFull()
                            ->columns(2),

                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Funil'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(
                        fn (string $state): string =>
                        DefaultStatus::getColorByDescription(statusDesc: $state)
                    )
                    ->searchable(
                        query: fn (FunnelService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchByStatus(query: $query, search: $search)
                    )
                    ->sortable(
                        query: fn (FunnelService $service, Builder $query, string $direction): Builder =>
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
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
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
                    ->label(__('Funil')),
                Infolists\Components\TextEntry::make('desciption')
                    ->label(__('Descrição')),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContactFunnels::route('/'),
            'create' => Pages\CreateContactFunnel::route('/create'),
            'edit'   => Pages\EditContactFunnel::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->byRoles(roles: [static::$role,]);
    }
}
