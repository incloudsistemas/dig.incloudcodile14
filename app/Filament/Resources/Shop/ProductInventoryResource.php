<?php

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Shop\ProductInventoryResource\Pages;
use App\Filament\Resources\Shop\ProductInventoryResource\RelationManagers;
use App\Models\Shop\ProductInventory;
use App\Services\Shop\ProductInventoryService;
use App\Services\Shop\ProductVariantItemService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductInventoryResource extends Resource
{
    protected static ?string $model = ProductInventory::class;

    protected static ?string $modelLabel = 'Controle de Estoque';

    protected static ?string $pluralModelLabel = 'Controles de Estoques';

    protected static ?string $navigationGroup = 'Loja';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Controles de Estoques';

    // heroicon-o-cube
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->relationship('variantItem')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'md'      => 2,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->label(__('SKU (Unidade de manutenção de estoque)'))
                                    ->unique(ignoreRecord: true)
                                    ->minLength(2)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('barcode')
                                    ->label(__('Código de barras (ISBN, UPC, GTIN etc.)'))
                                    ->unique(ignoreRecord: true)
                                    ->minLength(2)
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Checkbox::make('inventory_management')
                            ->label(__('Acompanhar quantidade'))
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\Checkbox::make('inventory_out_allowed')
                            ->label(__('Continuar vendendo mesmo sem estoque'))
                            ->helperText(__('Permite que os clientes comprem o item quando ele estiver fora de estoque (igual ou inferior a zero).'))
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('inventory_management')
                            )
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'md'      => 3,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('available')
                                    ->numeric()
                                    ->label(__('Estoque disponível'))
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(
                                        fn (ProductVariantItemService $service, callable $set, callable $get) =>
                                        static::updateInventoryTotal(service: $service, set: $set, get: $get),
                                    ),
                                Forms\Components\TextInput::make('committed')
                                    ->numeric()
                                    ->label(__('Comprometido'))
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(
                                        fn (ProductVariantItemService $service, callable $set, callable $get) =>
                                        static::updateInventoryTotal(service: $service, set: $set, get: $get),
                                    ),
                                Forms\Components\TextInput::make('to_receive')
                                    ->numeric()
                                    ->label(__('A ser recebido'))
                                    ->default(0)
                            ]),
                        Forms\Components\Fieldset::make(__('Estoque indisponível'))
                            ->schema([
                                Forms\Components\TextInput::make('unavailable_damaged')
                                    ->numeric()
                                    ->label(__('Danificado'))
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(
                                        fn (ProductVariantItemService $service, callable $set, callable $get) =>
                                        static::updateInventoryTotal(service: $service, set: $set, get: $get),
                                    ),
                                Forms\Components\TextInput::make('unavailable_quality_control')
                                    ->numeric()
                                    ->label(__('Controle de qualidade'))
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(
                                        fn (ProductVariantItemService $service, callable $set, callable $get) =>
                                        static::updateInventoryTotal(service: $service, set: $set, get: $get),
                                    ),
                                Forms\Components\TextInput::make('unavailable_safety')
                                    ->numeric()
                                    ->label(__('Estoque de segurança'))
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(
                                        fn (ProductVariantItemService $service, callable $set, callable $get) =>
                                        static::updateInventoryTotal(service: $service, set: $set, get: $get),
                                    ),
                                Forms\Components\TextInput::make('unavailable_other')
                                    ->numeric()
                                    ->label(__('Outro'))
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(
                                        fn (ProductVariantItemService $service, callable $set, callable $get) =>
                                        static::updateInventoryTotal(service: $service, set: $set, get: $get),
                                    ),
                            ])
                            ->columns(4),
                    ])
                    ->hidden(
                        fn (callable $get): bool =>
                        !$get('variantItem.inventory_management')
                    )
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->label(__('Total em estoque'))
                    ->helperText(__('Estoque completo que você tem em um local, incluindo a quantidade de estoque comprometido, indisponível e disponível.'))
                    ->default(0)
                    ->disabled()
                    ->hidden(
                        fn (callable $get): bool =>
                        !$get('variantItem.inventory_management')
                    ),
                Forms\Components\Group::make()
                    ->relationship('variantItem')
                    ->schema([
                        Forms\Components\TextInput::make('inventory_security_alert')
                            ->numeric()
                            ->label(__('Alerta de segurança'))
                            ->helperText(__('Estoque limite para seus produtos, que lhe alerta se o produto estará em breve fora de estoque.'))
                            ->mask(9999999)
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('inventory_management')
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\ImageColumn::make('variantItem.images')
                    ->label('')
                    ->size(45)
                    ->limit(1)
                    ->circular(),
                Tables\Columns\TextColumn::make('variantItem.display_name')
                    ->label(__('Produto'))
                    ->searchable(
                        query: fn (ProductInventoryService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchByProductVariantItem(query: $query, search: $search)
                    )
                    ->sortable(
                        query: fn (ProductInventoryService $service, Builder $query, string $direction): Builder =>
                        $service->tableSortByProductVariantItem(query: $query, direction: $direction)
                    ),
                Tables\Columns\TextColumn::make('variantItem.sku')
                    ->label(__('SKU'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('available')
                    ->label(__('Disponível'))
                    ->sortable()
                    ->alignment(Support\Enums\Alignment::Center),
                Tables\Columns\TextColumn::make('committed')
                    ->label(__('Comprometido'))
                    ->sortable()
                    ->alignment(Support\Enums\Alignment::Center),
                Tables\Columns\TextColumn::make('display_unavailable')
                    ->label(__('Indisponível'))
                    ->html(),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('Estoque total'))
                    ->tooltip('Disponível + Comprometido + Indisponível.')
                    ->alignment(Support\Enums\Alignment::Center),
                Tables\Columns\TextColumn::make('to_receive')
                    ->label(__('A ser recebido'))
                    ->sortable()
                    ->alignment(Support\Enums\Alignment::Center),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Cadastro'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Últ. atualização'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort(column: 'updated_at', direction: 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make()
                            ->mutateRecordDataUsing(
                                fn (ProductInventoryService $service, ProductInventory $inventory, array $data): array =>
                                $service->mutateRecordDataToEditUsing(inventory: $inventory, data: $data),
                            )
                            ->using(
                                fn (ProductInventoryService $service, ProductInventory $inventory, array $data): ProductInventory =>
                                $service->editAction(inventory: $inventory, data: $data),
                            ),
                    ])
                        ->dropdown(false),
                    // Tables\Actions\DeleteAction::make(),
                ])
                    ->label(__('Ações'))
                    ->icon('heroicon-m-chevron-down')
                    ->size(Support\Enums\ActionSize::ExtraSmall)
                    ->color('gray')
                    ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('Label')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make(__('Infos. Gerais'))
                            ->schema([
                                Infolists\Components\TextEntry::make('variantItem.display_name')
                                    ->label(__('Produto')),
                                // Infolists\Components\TextEntry::make('variantItem.product.productCategory.name')
                                //     ->label(__('Categoria'))
                                //     ->visible(
                                //         fn (?string $state): bool =>
                                //         !empty($state),
                                //     ),
                                // Infolists\Components\TextEntry::make('variantItem.product.productBrand.name')
                                //     ->label(__('Marca / Fabricante'))
                                //     ->visible(
                                //         fn (?string $state): bool =>
                                //         !empty($state),
                                //     ),
                                Infolists\Components\TextEntry::make('variantItem.sku')
                                    ->label(__('SKU (Unidade de manutenção de estoque)')),
                                Infolists\Components\TextEntry::make('variantItem.barcode')
                                    ->label(__('Código de barras (ISBN, UPC, GTIN etc.)')),
                                Infolists\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Infolists\Components\TextEntry::make('available')
                                            ->label(__('Estoque disponível')),
                                        Infolists\Components\TextEntry::make('committed')
                                            ->label(__('Comprometido')),
                                        Infolists\Components\TextEntry::make('to_receive')
                                            ->label(__('A ser recebido')),
                                    ]),
                                Infolists\Components\Fieldset::make(__('Estoque indisponível'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('unavailable_damaged')
                                            ->label(__('Danificado')),
                                        Infolists\Components\TextEntry::make('unavailable_quality_control')
                                            ->label(__('Controle de qualidade')),
                                        Infolists\Components\TextEntry::make('unavailable_safety')
                                            ->label(__('Estoque de segurança')),
                                        Infolists\Components\TextEntry::make('unavailable_other')
                                            ->label(__('Outro')),
                                    ])
                                    ->columns(4),
                                Infolists\Components\TextEntry::make('total')
                                    ->label(__('Total em estoque'))
                                    ->helperText(__('Disponível + Comprometido + Indisponível.')),
                                Infolists\Components\TextEntry::make('variantItem.inventory_out_allowed')
                                    ->label(__('Alerta de segurança'))
                                    ->visible(
                                        fn (?string $state): bool =>
                                        !empty($state),
                                    ),
                                Infolists\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Infolists\Components\TextEntry::make('created_at')
                                            ->label(__('Cadastro'))
                                            ->dateTime('d/m/Y H:i'),
                                        Infolists\Components\TextEntry::make('updated_at')
                                            ->label(__('Últ. atualização'))
                                            ->dateTime('d/m/Y H:i'),
                                    ]),
                            ]),
                        Infolists\Components\Tabs\Tab::make(__('Histórico de alterações'))
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('getListOfActivitiesOrderByDesc')
                                    ->label('')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('created_at')
                                            ->label(__('Data'))
                                            ->dateTime('d/m/Y H:i'),
                                        Infolists\Components\TextEntry::make('owner.name')
                                            ->label(__('Usuário')),
                                        Infolists\Components\TextEntry::make('description')
                                            ->label(__('Descrição'))
                                            ->visible(
                                                fn (?string $state): bool =>
                                                !empty($state),
                                            ),
                                        Infolists\Components\Fieldset::make(__('De:'))
                                            ->schema([
                                                Infolists\Components\TextEntry::make('changed_from.available')
                                                    ->label(__('Estoque disponível')),
                                                Infolists\Components\TextEntry::make('changed_from.committed')
                                                    ->label(__('Comprometido')),
                                                Infolists\Components\TextEntry::make('changed_from.to_receive')
                                                    ->label(__('A ser recebido')),
                                                Infolists\Components\Fieldset::make(__('Estoque indisponível'))
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('changed_from.unavailable_damaged')
                                                            ->label(__('Danificado')),
                                                        Infolists\Components\TextEntry::make('changed_from.unavailable_quality_control')
                                                            ->label(__('Ctrl. de qualidade')),
                                                        Infolists\Components\TextEntry::make('changed_from.unavailable_safety')
                                                            ->label(__('Estq. de segurança')),
                                                        Infolists\Components\TextEntry::make('changed_from.unavailable_other')
                                                            ->label(__('Outro')),
                                                    ])
                                                    ->columns(4),
                                            ])
                                            ->columns(3),
                                        Infolists\Components\Fieldset::make(__('Para:'))
                                            ->schema([
                                                Infolists\Components\TextEntry::make('changed_to.available')
                                                    ->label(__('Estoque disponível')),
                                                Infolists\Components\TextEntry::make('changed_to.committed')
                                                    ->label(__('Comprometido')),
                                                Infolists\Components\TextEntry::make('changed_to.to_receive')
                                                    ->label(__('A ser recebido')),
                                                Infolists\Components\Fieldset::make(__('Estoque indisponível'))
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('changed_to.unavailable_damaged')
                                                            ->label(__('Danificado')),
                                                        Infolists\Components\TextEntry::make('changed_to.unavailable_quality_control')
                                                            ->label(__('Ctrl. de qualidade')),
                                                        Infolists\Components\TextEntry::make('changed_to.unavailable_safety')
                                                            ->label(__('Estq. de segurança')),
                                                        Infolists\Components\TextEntry::make('changed_to.unavailable_other')
                                                            ->label(__('Outro')),
                                                    ])
                                                    ->columns(4),
                                            ])
                                            ->columns(3),
                                    ])
                                    // ->contained(false)
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    // ->contained(false)
                    ->columns(3)
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProductInventories::route('/'),
        ];
    }

    private static function updateInventoryTotal(ProductVariantItemService $service, callable $set, callable $get): void
    {
        $inventoryFields = [
            'available',
            'committed',
            'unavailable_damaged',
            'unavailable_quality_control',
            'unavailable_safety',
            'unavailable_other'
        ];

        foreach ($inventoryFields as $field) {
            $data['inventory'][$field] = $get($field);
        }

        $inventoryTotal = $service->getInventoryTotal(data: $data['inventory']);
        $set('total', $inventoryTotal);
    }
}
