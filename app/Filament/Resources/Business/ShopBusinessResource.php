<?php

namespace App\Filament\Resources\Business;

use App\Enums\Business\PaymentMethod;
use App\Filament\Resources\Business\ShopBusinessResource\Pages;
use App\Filament\Resources\Business\ShopBusinessResource\RelationManagers;
use App\Models\Business\Business;
use App\Models\Business\ShopBusiness;
use App\Models\Crm\Contacts\Contact;
use App\Models\Crm\Contacts\Individual;
use App\Models\Crm\Contacts\LegalEntity;
use App\Models\Crm\Funnels\FunnelStage;
use App\Models\Shop\ProductVariantItem;
use App\Services\Business\BusinessService;
use App\Services\Business\ShopBusinessService;
use App\Services\Crm\Contacts\ContactService;
use App\Services\Crm\Funnels\FunnelService;
use App\Services\UserService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ShopBusinessResource extends Resource
{
    protected static ?string $model = ShopBusiness::class;

    protected static ?array $roles = [2, 3]; // 2 - Ponto de venda, 3 - Loja virtual

    // protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Venda e Pedido';

    protected static ?string $pluralModelLabel = 'Vendas e Pedido';

    protected static ?string $navigationGroup = 'Loja';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Vendas / Pedidos';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Infos. Gerais'))
                    ->description(__('Visão geral e informações fundamentais sobre a venda/pedido.'))
                    ->schema([
                        // 2 - [Loja] Ponto de Venda (Point of Sale)
                        Forms\Components\Hidden::make('role')
                            ->default(2)
                            ->visibleOn('create'),
                        Forms\Components\Select::make('contact_id')
                            ->label(__('Contato'))
                            ->relationship(
                                name: 'contact',
                                modifyQueryUsing: fn (ContactService $service, Builder $query): Builder =>
                                $service->forceScopeActiveStatus($query),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Model $record) =>
                                $record->contactable?->name ?? null
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->when(
                                auth()->user()->can('Cadastrar [CRM] Contatos P. Físicas') ||
                                    auth()->user()->can('Cadastrar [CRM] Contatos P. Jurídicas'),
                                fn (Forms\Components\Select $component): Forms\Components\Select =>
                                $component->suffixAction(
                                    Forms\Components\Actions\Action::make('contact')
                                        ->icon('heroicon-o-plus')
                                        ->form([
                                            Forms\Components\Grid::make(['default' => 2])
                                                ->schema([
                                                    Forms\Components\Select::make('contactable_type')
                                                        ->label(__('Tipo de contato'))
                                                        ->options(function (): array {
                                                            if (
                                                                auth()->user()->can('Cadastrar [CRM] Contatos P. Físicas') &&
                                                                auth()->user()->can('Cadastrar [CRM] Contatos P. Jurídicas')
                                                            ) {
                                                                return [
                                                                    'crm_contact_individuals'    => 'P. Física',
                                                                    'crm_contact_legal_entities' => 'P. Jurídica',
                                                                ];
                                                            }

                                                            if (auth()->user()->can('Cadastrar [CRM] Contatos P. Físicas')) {
                                                                return [
                                                                    'crm_contact_individuals' => 'P. Física',
                                                                ];
                                                            }

                                                            if (auth()->user()->can('Cadastrar [CRM] Contatos P. Jurídicas')) {
                                                                return [
                                                                    'crm_contact_legal_entities' => 'P. Jurídica',
                                                                ];
                                                            }

                                                            return [];
                                                        })
                                                        ->default(
                                                            fn (): string =>
                                                            auth()->user()->can('Cadastrar [CRM] Contatos P. Físicas')
                                                                ? 'crm_contact_individuals'
                                                                : 'crm_contact_legal_entities',
                                                        )
                                                        ->required()
                                                        ->live()
                                                        ->selectablePlaceholder(false)
                                                        ->native(false)
                                                        ->columnSpanFull(),
                                                    Forms\Components\TextInput::make('name')
                                                        ->label(__('Nome'))
                                                        ->required()
                                                        ->minLength(2)
                                                        ->maxLength(255)
                                                        ->columnSpanFull(),
                                                    Forms\Components\TextInput::make('email')
                                                        ->label(__('Email'))
                                                        ->email()
                                                        ->unique(Individual::class, 'email', ignoreRecord: true)
                                                        ->maxLength(255)
                                                        ->visible(
                                                            fn (callable $get): bool =>
                                                            $get('contactable_type') == "crm_contact_individuals"
                                                        ),
                                                    Forms\Components\TextInput::make('email')
                                                        ->label(__('Email'))
                                                        ->email()
                                                        ->unique(LegalEntity::class, 'email', ignoreRecord: true)
                                                        ->maxLength(255)
                                                        ->visible(
                                                            fn (callable $get): bool =>
                                                            $get('contactable_type') == "crm_contact_legal_entities"
                                                        ),
                                                    Forms\Components\Hidden::make('phones.0.name')
                                                        ->default(null),
                                                    Forms\Components\TextInput::make('phones.0.number')
                                                        ->label(__('Nº do telefone'))
                                                        ->mask(
                                                            Support\RawJs::make(<<<'JS'
                                                                $input.length === 14 ? '(99) 9999-9999' : '(99) 99999-9999'
                                                            JS)
                                                        )
                                                        ->live(onBlur: true)
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('cpf')
                                                        ->label(__('CPF'))
                                                        ->mask('999.999.999-99')
                                                        ->unique(Individual::class, 'cpf', ignoreRecord: true)
                                                        ->maxLength(255)
                                                        ->visible(
                                                            fn (callable $get): bool =>
                                                            $get('contactable_type') == 'crm_contact_individuals'
                                                        ),
                                                    Forms\Components\DatePicker::make('birth_date')
                                                        ->label(__('Dt. nascimento'))
                                                        ->format('d/m/Y')
                                                        ->maxDate(now())
                                                        ->visible(
                                                            fn (callable $get): bool =>
                                                            $get('contactable_type') == 'crm_contact_individuals'
                                                        ),
                                                    Forms\Components\TextInput::make('cnpj')
                                                        ->label(__('CNPJ'))
                                                        ->mask('99.999.999/9999-99')
                                                        ->unique(LegalEntity::class, 'cnpj', ignoreRecord: true)
                                                        ->maxLength(255)
                                                        ->visible(
                                                            fn (callable $get): bool =>
                                                            $get('contactable_type') == 'crm_contact_legal_entities'
                                                        ),
                                                    Forms\Components\TextInput::make('url')
                                                        ->label(__('URL do site'))
                                                        ->url()
                                                        // ->prefix('https://')
                                                        ->helperText('https://...')
                                                        ->maxLength(255)
                                                        ->visible(
                                                            fn (callable $get): bool =>
                                                            $get('contactable_type') == 'crm_contact_legal_entities'
                                                        ),

                                                ])
                                        ])
                                        ->action(
                                            function (array $data, callable $set): void {
                                                if ($data['contactable_type'] === 'crm_contact_individuals') {
                                                    $contactable = Individual::create($data);
                                                } elseif ($data['contactable_type'] === 'crm_contact_legal_entities') {
                                                    $contactable = LegalEntity::create($data);
                                                }

                                                if ($contactable) {
                                                    $contact = $contactable->contact()->create([
                                                        'user_id' => auth()->user()->id,
                                                    ]);
                                                    $set('contact_id', $contact->id);
                                                }
                                            }
                                        ),
                                ),
                            )
                            ->columnSpanFull(),
                        Forms\Components\Select::make('funnel_id')
                            ->label(__('Funil'))
                            ->relationship(
                                name: 'funnels',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (FunnelService $service, Builder $query): Builder =>
                                $service->getActiveFunnelsByRoles(query: $query, roles: [1,]), // 1 - business
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->default(3)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('funnel_stage_id')
                            ->label('')
                            ->options(
                                fn (FunnelService $service, callable $get): Collection =>
                                $service->getFunnelStagesByFunnel(funnelId: $get('funnel_id')),
                            )
                            ->searchable()
                            ->required()
                            ->default(19)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Descrição/observações da venda/pedido'))
                            ->rows(4)
                            ->minLength(2)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('user_id')
                            ->label(__('Angariador'))
                            ->relationship(
                                name: 'owner',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (UserService $servive): Builder =>
                                $servive->forceScopeActiveStatus()
                            )
                            ->searchable()
                            ->preload()
                            ->default(auth()->user()->id)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('business_at')
                            ->label(__('Dt. venda/pedido'))
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->default(now())
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make(__('Produtos'))
                    ->description(__('Visualize todos os produtos adicionados edite quantidades ou remova itens conforme necessário.'))
                    ->schema([
                        Forms\Components\Repeater::make('traded_items')
                            ->label(__('Carrinho de compras'))
                            ->schema([
                                Forms\Components\Select::make('product_variant_item_id')
                                    ->label(__('Produto'))
                                    ->searchable()
                                    ->preload()
                                    ->getSearchResultsUsing(
                                        fn (ShopBusinessService $service, string $search): array =>
                                        $service->getProductVariantOptionsBySearch(search: $search),
                                    )
                                    ->getOptionLabelUsing(
                                        fn (ShopBusinessService $service, string $value): string =>
                                        $service->getProductVariantOptionLabel(value: $value),
                                    )
                                    ->required()
                                    ->live(debounce: 1000)
                                    ->afterStateUpdated(
                                        function (
                                            ShopBusinessService $service,
                                            callable $get,
                                            callable $set,
                                            ?string $state
                                        ): void {
                                            $variantInfos = $service->getProductVariantInfos(variantItemId: $state);
                                            $set('unit_price', $variantInfos['unit_price']);
                                            $set('unit_cost', $variantInfos['unit_cost']);
                                            $set('quantity', $variantInfos['default_quantity']);
                                            $set('price', $variantInfos['price']);
                                            $set('cost', $variantInfos['cost']);
                                            $set('inventory_available', $variantInfos['inventory_available']);

                                            $totalValues = $service->getTotalPriceOfAllVariants(
                                                tradedItems: $get('../../traded_items'),
                                                discount: $get('../../discount')
                                            );
                                            $set('../../price', $totalValues['price']);
                                            $set('../../cost', $totalValues['cost']);
                                        }
                                    )
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Forms\Components\TextInput::make('unit_price')
                                            ->label(__('Preço unitário'))
                                            // ->numeric()
                                            ->prefix('R$')
                                            ->mask(
                                                Support\RawJs::make(<<<'JS'
                                                    $money($input, ',')
                                                JS)
                                            )
                                            ->placeholder('0,00')
                                            ->required()
                                            ->maxValue(42949672.95)
                                            ->disabled()
                                            ->dehydrated(),
                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->label(__('Quantidade'))
                                            // __('Qtde. total em estoque: ')
                                            ->helperText(
                                                fn (callable $get): ?string =>
                                                $get('product_variant_item_id')
                                                    ? __('Qtde. em estoque: ') . $get('inventory_available')
                                                    : null
                                            )
                                            // ->default(0)
                                            ->placeholder(
                                                fn (callable $get): ?string =>
                                                !$get('product_variant_item_id') ? __('Selecione o produto') : null
                                            )
                                            ->required()
                                            ->minValue(1)
                                            ->maxValue(
                                                fn (callable $get): ?int =>
                                                $get('inventory_available')
                                            )
                                            ->disabled(
                                                fn (callable $get): bool =>
                                                !$get('product_variant_item_id')
                                            )
                                            ->live()
                                            ->afterStateUpdated(
                                                function (
                                                    ShopBusinessService $service,
                                                    callable $get,
                                                    callable $set,
                                                    ?string $state
                                                ): void {
                                                    $totalVariantValues = $service->getTotalPriceByVariantQuantity(
                                                        unitPrice: $get('unit_price'),
                                                        unitCost: $get('unit_cost'),
                                                        quantity: $state
                                                    );
                                                    $set('price', $totalVariantValues['price']);
                                                    $set('cost', $totalVariantValues['cost']);

                                                    $totalValues = $service->getTotalPriceOfAllVariants(
                                                        tradedItems: $get('../../traded_items'),
                                                        discount: $get('../../discount')
                                                    );
                                                    $set('../../price', $totalValues['price']);
                                                    $set('../../cost', $totalValues['cost']);
                                                }
                                            ),
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
                                            ->required()
                                            ->maxValue(42949672.95)
                                            ->disabled()
                                            ->dehydrated(),
                                    ]),
                                Forms\Components\Hidden::make('unit_cost'),
                                Forms\Components\Hidden::make('cost'),
                                Forms\Components\Hidden::make('inventory_available'),
                            ])
                            ->itemLabel(
                                fn (array $state): ?string =>
                                $state['description'] ?? null
                            )
                            ->addActionLabel(__('Adicionar produto'))
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->collapseAllAction(
                                fn (Forms\Components\Actions\Action $action) =>
                                $action->label(__('Minimizar todos'))
                            )
                            ->deleteAction(
                                function (
                                    Forms\Components\Actions\Action $action,
                                    ShopBusinessService $service,
                                    callable $get,
                                    callable $set
                                ): void {
                                    $action->requiresConfirmation();

                                    $totalValues = $service->getTotalPriceOfAllVariants(
                                        tradedItems: $get('traded_items'),
                                        discount: $get('discount')
                                    );
                                    $set('price', $totalValues['price']);
                                    $set('cost', $totalValues['cost']);
                                }
                            )
                            ->columnSpanFull()
                            ->columns(2),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make(__('Condições de Pagamentos'))
                    ->description(__('Informe o método, revise e confirme as condições de pagamento.'))
                    ->schema([
                        Forms\Components\TextInput::make('discount')
                            ->label(__('Desconto'))
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
                                function (ShopBusinessService $service, callable $get, callable $set): void {
                                    $totalValues = $service->getTotalPriceOfAllVariants(
                                        tradedItems: $get('traded_items'),
                                        discount: $get('discount')
                                    );
                                    $set('price', $totalValues['price']);
                                    $set('cost', $totalValues['cost']);
                                }
                            ),
                        Forms\Components\TextInput::make('price')
                            ->label(__('Preço total'))
                            // ->numeric()
                            ->prefix('R$')
                            ->mask(
                                Support\RawJs::make(<<<'JS'
                                    $money($input, ',')
                                JS)
                            )
                            ->placeholder('0,00')
                            ->required()
                            ->maxValue(42949672.95)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Hidden::make('cost'),
                        Forms\Components\Select::make('payment_method')
                            ->label(__('Método de pagamento'))
                            ->options(PaymentMethod::asSelectArray())
                            ->required()
                            ->in(PaymentMethod::getValues())
                            ->native(false),
                        Forms\Components\Select::make('num_installments')
                            ->label(__('Condições de pagamento'))
                            ->options([
                                0  => 'À vista',
                                1  => '1x',
                                2  => '2x',
                                3  => '3x',
                                4  => '4x',
                                5  => '5x',
                                6  => '6x',
                                7  => '7x',
                                8  => '8x',
                                9  => '9x',
                                10 => '10x',
                                11 => '11x',
                                12 => '12x',
                                13 => '13x',
                                14 => '14x',
                                15 => '15x',
                                16 => '16x',
                                17 => '17x',
                                18 => '18x',
                                19 => '19x',
                                20 => '20x',
                                21 => '21x',
                                22 => '22x',
                                23 => '23x',
                                24 => '24x'
                            ])
                            ->default(0)
                            ->required()
                            ->native(false)
                            ->selectablePlaceholder(false),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('#ID'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.contactable.name')
                    ->label(__('Contato'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.contactable.email')
                    ->label(__('Email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('contact.contactable.display_main_phone')
                    ->label(__('Telefone'))
                    // ->searchable(
                    //     query: fn (ContactService $service, Builder $query, string $search): Builder =>
                    //     $service->tableSearchByPhone(query: $query, search: $search)
                    // )
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('display_price')
                    ->label(__('Preço (R$)'))
                    ->sortable(
                        query: fn (BusinessService $service, Builder $query, string $direction): Builder =>
                        $service->tableSortByPrice(query: $query, direction: $direction)
                    ),
                Tables\Columns\TextColumn::make('display_payment_method')
                    ->label(__('Método de pagamento'))
                    ->description(
                        fn (Business $record): string => $record->display_num_installments
                    )
                    ->searchable(
                        query: fn (BusinessService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchByPaymentMethod(query: $query, search: $search)
                    )
                    ->sortable(
                        query: fn (BusinessService $service, Builder $query, string $direction): Builder =>
                        $service->tableSortByPaymentMethod(query: $query, direction: $direction)
                    ),
                // Tables\Columns\TextColumn::make('display_num_installments')
                //     ->label(__('Condições de pagamento')),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label(__('Angariador'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('business_at')
                    ->label(__('Dt. venda/pedido'))
                    ->dateTime('d/m/Y H:i')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Cadastro'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Últ. atualização'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort(
                fn (BusinessService $service, Builder $query): Builder =>
                $service->tableDefaultSort(query: $query)
            )
            ->filters([
                Tables\Filters\SelectFilter::make('owners')
                    ->label(__('Angariadores'))
                    ->options(
                        fn (BusinessService $service): array =>
                        $service->tableFilterGetOptionsByOwners(),
                    )
                    ->query(
                        fn (BusinessService $service, Builder $query, array $data): Builder =>
                        $service->tableFilterGetQueryByOwners(query: $query, data: $data)
                    )
                    ->multiple(),
                Tables\Filters\Filter::make('business_at')
                    ->label(__('Dt. venda/pedido'))
                    ->form([
                        Forms\Components\DatePicker::make('business_from')
                            ->label(__('Dt. venda/pedido de'))
                            ->live(debounce: 500)
                            ->afterStateUpdated(
                                function (callable $get, callable $set, ?string $state): void {
                                    if (empty($get('business_until'))) {
                                        $set('business_until', $state);
                                    }
                                }
                            ),
                        Forms\Components\DatePicker::make('business_until')
                            ->label(__('Dt. venda/pedido até'))
                            ->live(debounce: 500)
                            ->afterStateUpdated(
                                function (callable $get, callable $set, ?string $state): void {
                                    if (empty($get('business_from'))) {
                                        $set('business_from', $state);
                                    }
                                }
                            ),
                    ])
                    ->query(
                        fn (BusinessService $service, Builder $query, array $data): Builder =>
                        $service->tableFilterByBusinessAt(query: $query, data: $data)
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                    ])
                        ->dropdown(false),
                    Tables\Actions\DeleteAction::make()
                        ->before(
                            fn (ShopBusinessService $service, ShopBusiness $business) =>
                            $service->retrieveInventory(business: $business)
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
                    // Tables\Actions\DeleteBulkAction::make(),
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
                Infolists\Components\Tabs::make('Label')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make(__('Infos. Gerais'))
                            ->schema([
                                Infolists\Components\TextEntry::make('id')
                                    ->label(__('#ID')),
                                Infolists\Components\TextEntry::make('contact.contactable.name')
                                    ->label(__('Contato')),
                                Infolists\Components\TextEntry::make('owner.name')
                                    ->label(__('Angariador')),
                                Infolists\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Infolists\Components\TextEntry::make('funnels')
                                            ->label(__('Funil / Estágio'))
                                            ->formatStateUsing(
                                                fn (Model $record): string =>
                                                $record->funnels[0]->name . " / " . $record->funnelStages[0]->name
                                            ),
                                        // Infolists\Components\TextEntry::make('funnels.0.name')
                                        //     ->label(__('Funil')),
                                        // Infolists\Components\TextEntry::make('funnelStages.0.name')
                                        //     ->label(__('Estágio do Funil')),
                                        Infolists\Components\TextEntry::make('payment_method')
                                            ->label(__('Método de pagamento'))
                                            ->formatStateUsing(
                                                fn (Model $record): string =>
                                                $record->display_payment_method . " " . $record->display_num_installments
                                            ),
                                        // Infolists\Components\TextEntry::make('display_payment_method')
                                        //     ->label(__('Método de pagamento')),
                                        // Infolists\Components\TextEntry::make('display_num_installments')
                                        //     ->label(__('Condições de pagamento')),
                                    ]),
                                Infolists\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Infolists\Components\TextEntry::make('display_cost')
                                            ->label(__('Custo total (R$)')),
                                        Infolists\Components\TextEntry::make('display_discount')
                                            ->label(__('Desconto (R$)')),
                                        Infolists\Components\TextEntry::make('display_price')
                                            ->label(__('Preço total (R$)')),
                                    ]),
                                Infolists\Components\TextEntry::make('description')
                                    ->label(__('Descrição/observações da venda/pedido'))
                                    ->visible(
                                        fn (?string $state): bool  =>
                                        !empty($state),
                                    )
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('business_at')
                                    ->label(__('Dt. venda/pedido'))
                                    ->dateTime('d/m/Y H:i'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('Cadastro'))
                                    ->dateTime('d/m/Y H:i'),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('Últ. atualização'))
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                        Infolists\Components\Tabs\Tab::make(__('Produtos'))
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('tradedItems')
                                    ->label('')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('businessable.display_name')
                                            ->label(__('Produto'))
                                            ->columnSpanFull(),
                                        Infolists\Components\TextEntry::make('display_unit_price')
                                            ->label(__('Preço unitário (R$)')),
                                        Infolists\Components\TextEntry::make('quantity')
                                            ->label(__('Quantidade')),
                                        Infolists\Components\TextEntry::make('display_price')
                                            ->label(__('Preço (R$)')),
                                    ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListShopBusinesses::route('/'),
            'create' => Pages\CreateShopBusiness::route('/create'),
            'edit'   => Pages\EditShopBusiness::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->byRoles(roles: static::$roles,);

        if (!auth()->user()->hasRole(['Superadministrador', 'Administrador'])) {
            return $query->where('user_id', auth()->user()->id);
        }

        return $query;
    }
}
