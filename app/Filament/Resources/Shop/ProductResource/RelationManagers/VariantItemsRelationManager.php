<?php

namespace App\Filament\Resources\Shop\ProductResource\RelationManagers;

use App\Enums\DefaultStatus;
use App\Models\Shop\ProductVariantItem;
use App\Services\Shop\ProductVariantItemService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class VariantItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'variantItems';

    protected static ?string $title = 'Variantes';

    protected static ?string $modelLabel = 'Variante';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('name')
                //     ->label(__('Nome da variante'))
                //     ->required()
                //     ->minLength(2)
                //     ->maxLength(255)
                //     ->disabled()
                //     ->columnSpanFull(),
                Forms\Components\Fieldset::make(__('Precificação'))
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label(__('Preço'))
                            // ->numeric()
                            ->prefix('R$')
                            ->mask(
                                Support\RawJs::make(<<<'JS'
                                    $money($input, ',')
                                JS)
                            )
                            ->placeholder('0,00')
                            ->maxValue(42949672.95)
                            ->live(debounce: 1000)
                            ->afterStateUpdated(
                                function (ProductVariantItemService $service, callable $set, callable $get, ?string $state): void {
                                    $profitAndMargin = $service->getProfitAndMargin(price: $state, cost: $get('unit_cost'));
                                    $set('profit', $profitAndMargin['profit']);
                                    $set('profit_margin', $profitAndMargin['profit_margin']);
                                }
                            ),
                        Forms\Components\TextInput::make('compare_at_price')
                            ->label(__('Comparação de preços'))
                            ->helperText(__('Para exibir um markdown, insira um valor maior que o preço. Em geral, ele aparece riscado.'))
                            // ->numeric()
                            ->prefix('R$')
                            ->mask(
                                Support\RawJs::make(<<<'JS'
                                    $money($input, ',')
                                JS)
                            )
                            ->placeholder('0,00')
                            ->maxValue(42949672.95),
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'md'      => 3,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('unit_cost')
                                    ->label(__('Custo por item'))
                                    // ->numeric()
                                    ->prefix('R$')
                                    ->mask(
                                        Support\RawJs::make(<<<'JS'
                                            $money($input, ',')
                                        JS)
                                    )
                                    ->placeholder('0,00')
                                    ->maxValue(42949672.95)
                                    ->live(debounce: 1000)
                                    ->afterStateUpdated(
                                        function (ProductVariantItemService $service, callable $set, callable $get, ?string $state): void {
                                            $profitAndMargin = $service->getProfitAndMargin(price: $get('price'), cost: $state);
                                            $set('profit', $profitAndMargin['profit']);
                                            $set('profit_margin', $profitAndMargin['profit_margin']);
                                        }
                                    ),
                                Forms\Components\TextInput::make('profit')
                                    ->label(__('Lucro'))
                                    ->prefix('R$')
                                    ->disabled(),
                                Forms\Components\TextInput::make('profit_margin')
                                    ->label(__('Margem'))
                                    ->suffix('%')
                                    ->disabled(),
                            ])
                    ]),
                Forms\Components\Fieldset::make(__('Controle de estoque'))
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
                        Forms\Components\Checkbox::make('inventory_management')
                            ->label(__('Acompanhar quantidade'))
                            ->default(true)
                            ->live()
                            ->hidden(
                                fn (): bool =>
                                !auth()->user()->can('Editar [Shop] Estoques')
                            )
                            ->columnSpanFull(),
                        Forms\Components\Checkbox::make('inventory_out_allowed')
                            ->label(__('Continuar vendendo mesmo sem estoque'))
                            ->helperText(__('Permite que os clientes comprem o item quando ele estiver fora de estoque (igual ou inferior a zero).'))
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('inventory_management') || !auth()->user()->can('Editar [Shop] Estoques')
                            )
                            ->columnSpanFull(),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Grid::make([
                                    'default' => 1,
                                    'md'      => 3,
                                ])
                                    ->schema([
                                        Forms\Components\TextInput::make('inventory.available')
                                            ->numeric()
                                            ->label(__('Estoque disponível'))
                                            ->default(0)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(
                                                function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                    $inventoryTotal = $service->getInventoryTotal(data: $get('inventory'));
                                                    $set('inventory.total', $inventoryTotal);
                                                }
                                            ),
                                        Forms\Components\TextInput::make('inventory.committed')
                                            ->numeric()
                                            ->label(__('Comprometido'))
                                            ->default(0)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(
                                                function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                    $inventoryTotal = $service->getInventoryTotal(data: $get('inventory'));
                                                    $set('inventory.total', $inventoryTotal);
                                                }
                                            ),
                                        Forms\Components\TextInput::make('inventory.to_receive')
                                            ->numeric()
                                            ->label(__('A ser recebido'))
                                            ->default(0)
                                    ]),
                                Forms\Components\Fieldset::make(__('Estoque indisponível'))
                                    ->schema([
                                        Forms\Components\TextInput::make('inventory.unavailable_damaged')
                                            ->numeric()
                                            ->label(__('Danificado'))
                                            ->default(0)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(
                                                function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                    $inventoryTotal = $service->getInventoryTotal(data: $get('inventory'));
                                                    $set('inventory.total', $inventoryTotal);
                                                }
                                            ),
                                        Forms\Components\TextInput::make('inventory.unavailable_quality_control')
                                            ->numeric()
                                            ->label(__('Controle de qualidade'))
                                            ->default(0)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(
                                                function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                    $inventoryTotal = $service->getInventoryTotal(data: $get('inventory'));
                                                    $set('inventory.total', $inventoryTotal);
                                                }
                                            ),
                                        Forms\Components\TextInput::make('inventory.unavailable_safety')
                                            ->numeric()
                                            ->label(__('Estoque de segurança'))
                                            ->default(0)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(
                                                function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                    $inventoryTotal = $service->getInventoryTotal(data: $get('inventory'));
                                                    $set('inventory.total', $inventoryTotal);
                                                }
                                            ),
                                        Forms\Components\TextInput::make('inventory.unavailable_other')
                                            ->numeric()
                                            ->label(__('Outro'))
                                            ->default(0)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(
                                                function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                    $inventoryTotal = $service->getInventoryTotal(data: $get('inventory'));
                                                    $set('inventory.total', $inventoryTotal);
                                                }
                                            ),
                                    ])
                                    ->columns(4),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('inventory_management') || !auth()->user()->can('Editar [Shop] Estoques')
                            )
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('inventory.total')
                            ->numeric()
                            ->label(__('Total em estoque'))
                            ->helperText(__('Estoque completo que você tem em um local, incluindo a quantidade de estoque comprometido, indisponível e disponível.'))
                            ->default(0)
                            ->disabled()
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('inventory_management') || !auth()->user()->can('Editar [Shop] Estoques')
                            ),
                        Forms\Components\TextInput::make('inventory_security_alert')
                            ->numeric()
                            ->label(__('Alerta de segurança'))
                            ->helperText(__('Estoque limite para seus produtos, que lhe alerta se o produto estará em breve fora de estoque.'))
                            ->mask(9999999)
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('inventory_management') || !auth()->user()->can('Editar [Shop] Estoques')
                            ),
                    ]),
                Forms\Components\Fieldset::make(__('Frete'))
                    ->schema([
                        Forms\Components\Checkbox::make('requires_shipping')
                            ->label(__('Este produto exige frete'))
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'lg'      => 4,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('weight')
                                    ->label(__('Peso'))
                                    ->numeric()
                                    ->mask(9999999)
                                    ->suffix(__('gramas')),
                                Forms\Components\TextInput::make('dimensions.height')
                                    ->label(__('Altura'))
                                    ->numeric()
                                    ->mask(9999999)
                                    ->suffix(__('cm')),
                                Forms\Components\TextInput::make('dimensions.width')
                                    ->label(__('Largura'))
                                    ->numeric()
                                    ->mask(9999999)
                                    ->suffix(__('cm')),
                                Forms\Components\TextInput::make('dimensions.length')
                                    ->label(__('Comprimento'))
                                    ->numeric()
                                    ->mask(9999999)
                                    ->suffix(__('cm')),
                                Forms\Components\Placeholder::make('')
                                    ->content(__('Os valores devem ser inteiros. Se for decimal, arredonde para o número inteiro mais próximo.'))
                                    ->columnSpanFull(),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('requires_shipping')
                            ),
                    ]),
                Forms\Components\FileUpload::make('images')
                    ->label(__('Upload das imagens'))
                    ->helperText(__('Tipos de arquivo permitidos: .png, .jpg, .jpeg, .gif. // Máx. 1920x1080px // 5 mb.'))
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory("product-variant-items/{$this->ownerRecord->slug}")
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file, callable $get): string =>
                        (string) str('-' . md5(uniqid()) . '-' . time() . '.' . $file->extension())
                            ->prepend($this->ownerRecord->slug),
                    )
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->imageResizeUpscale(false)
                    ->maxSize(5120)
                    ->downloadable(),
                Forms\Components\Select::make('status')
                    ->label(__('Status'))
                    ->options(DefaultStatus::asSelectArray())
                    ->default(1)
                    ->required()
                    ->in(DefaultStatus::getValues())
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->striped()
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('')
                    ->size(45)
                    ->limit(1)
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Variante'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label(__('SKU'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('barcode')
                    ->label(__('Cód. de barras'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_price')
                    ->label(__('Preço (R$)'))
                    ->sortable(
                        query: fn (ProductVariantItemService $service, Builder $query, string $direction): Builder =>
                        $service->tableSortByPrice(query: $query, direction: $direction)
                    ),
                Tables\Columns\TextColumn::make('inventory.available')
                    ->label(__('Estoque disponível'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(
                        fn (string $state): string =>
                        DefaultStatus::getColorByDescription(statusDesc: $state)
                    )
                    ->searchable(
                        query: fn (ProductVariantItemService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchByStatus(query: $query, search: $search)
                    )
                    ->sortable(
                        query: fn (ProductVariantItemService $service, Builder $query, string $direction): Builder =>
                        $service->tableSortByStatus(query: $query, direction: $direction)
                    ),
            ])
            ->reorderable('order')
            ->defaultSort(column: 'order', direction: 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options(DefaultStatus::asSelectArray()),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        // Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make()
                            ->mutateRecordDataUsing(
                                fn (ProductVariantItemService $service, ProductVariantItem $variantItem, array $data): array =>
                                $service->mutateRecordDataToEditUsing(variantItem: $variantItem, data: $data),
                            )
                            ->using(
                                fn (ProductVariantItemService $service, ProductVariantItem $variantItem, array $data): ProductVariantItem =>
                                $service->editAction(variantItem: $variantItem, data: $data),
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
                    Tables\Actions\BulkAction::make('update_images')
                        ->label(__('Atualizar imagens'))
                        ->icon('heroicon-o-photo')
                        ->form([
                            Forms\Components\FileUpload::make('images')
                                ->label(__('Upload das imagens'))
                                ->helperText(__('Tipos de arquivo permitidos: .png, .jpg, .jpeg, .gif. // Máx. 1920x1080px // 5 mb.'))
                                ->image()
                                ->multiple()
                                ->disk('public')
                                ->directory("product-variant-items/{$this->ownerRecord->slug}")
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file): string =>
                                    (string) str('-' . md5(uniqid()) . '-' . time() . '.' . $file->extension())
                                        ->prepend($this->ownerRecord->slug),
                                )
                                ->required()
                                ->imageResizeMode('contain')
                                ->imageResizeTargetWidth('1920')
                                ->imageResizeTargetHeight('1080')
                                ->imageResizeUpscale(false)
                                ->maxSize(5120)
                                ->downloadable()
                                ->columnSpanFull(),
                        ])
                        ->action(
                            fn (Collection $records, array $data) =>
                            $records->each->update($data)
                        ),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        // return ($ownerRecord->variantItems->count() > 0 && $ownerRecord->variantItems[0]->name !== 'Default Title')
        //     ? true
        //     : false;

        return $ownerRecord->has_variants;
    }
}
