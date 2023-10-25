<?php

namespace App\Filament\Resources\Shop;

use App\Enums\Cms\DefaultPostStatus;
use App\Filament\Resources\Shop\ProductResource\Pages;
use App\Filament\Resources\Shop\ProductResource\RelationManagers;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use App\Models\Shop\ProductVariantItem;
use App\Services\Cms\PostService;
use App\Services\Shop\ProductBrandService;
use App\Services\Shop\ProductCategoryService;
use App\Services\Shop\ProductService;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Produto';

    protected static ?string $navigationGroup = 'Loja';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-gift-top';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Infos. Gerais'))
                    ->description(__('Visão geral e informações fundamentais sobre o produto.'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Nome do produto'))
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->live(debounce: 1000)
                            ->afterStateUpdated(
                                fn (callable $set, ?string $state): ?string =>
                                $set('slug', Str::slug($state))
                            )
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        // Forms\Components\TextInput::make('subtitle')
                        //     ->label(__('Subtítulo'))
                        //     ->minLength(2)
                        //     ->maxLength(255)
                        //     ->columnSpanFull(),
                        Forms\Components\Select::make('category_id')
                            ->label(__('Categoria do produto'))
                            ->relationship(
                                name: 'productCategory',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (ProductCategoryService $service): Builder =>
                                $service->forceScopeActiveStatus()
                            )
                            ->searchable()
                            ->preload()
                            ->when(
                                auth()->user()->can('Cadastrar [Shop] Categorias'),
                                fn (Forms\Components\Select $component): Forms\Components\Select =>
                                $component->createOptionForm([
                                    Forms\Components\Grid::make(['default' => 2])
                                        ->schema([
                                            Forms\Components\Select::make('category_id')
                                                ->label(__('Categoria parental'))
                                                ->relationship(
                                                    name: 'mainCategory',
                                                    titleAttribute: 'name',
                                                    modifyQueryUsing: fn (ProductCategoryService $service, ProductCategory $category): Builder =>
                                                    $service->getActiveCategoriesIgnoreRecord(category: $category)
                                                )
                                                ->searchable()
                                                ->preload()
                                                ->columnSpanFull(),
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
                                        ]),
                                ])
                            )
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('excerpt')
                            ->label(__('Descrição resumida'))
                            ->rows(4)
                            ->minLength(2)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make(__('Galeria de Imagens e Vídeos'))
                    ->description(__('Adicione e gerencie as imagens e vídeos do produto.'))
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                            ->label(__('Upload das imagens'))
                            ->helperText(__('Tipos de arquivo permitidos: .png, .jpg, .jpeg, .gif. // Máx. 1920x1080px // 5 mb.'))
                            ->collection('images')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->responsiveImages()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file, callable $get): string =>
                                (string) str('-' . md5(uniqid()) . '-' . time() . '.' . $file->extension())
                                    ->prepend($get('slug')),
                            )
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->imageResizeUpscale(false)
                            ->maxSize(5120)
                            ->downloadable(),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('videos')
                            ->label(__('Upload dos vídeos'))
                            ->helperText(__('Tipo de arquivo permitido: .mp4. // Máx. 25 mb.'))
                            ->collection('videos')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file, callable $get): string =>
                                (string) str('-' . md5(uniqid()) . '-' . time() . '.' . $file->extension())
                                    ->prepend($get('slug')),
                            )
                            ->multiple()
                            ->acceptedFileTypes(['video/mp4'])
                            ->maxSize(25600)
                            ->downloadable(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('Precificação'))
                            ->description(__('Defina o preço de venda para seu produto.'))
                            ->schema([
                                Forms\Components\TextInput::make('default_variant.price')
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
                                            $profitAndMargin = $service->getProfitAndMargin(price: $state, cost: $get('default_variant.unit_cost'));
                                            $set('profit', $profitAndMargin['profit']);
                                            $set('profit_margin', $profitAndMargin['profit_margin']);
                                        }
                                    ),
                                Forms\Components\TextInput::make('default_variant.compare_at_price')
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
                                        Forms\Components\TextInput::make('default_variant.unit_cost')
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
                                                    $profitAndMargin = $service->getProfitAndMargin(price: $get('default_variant.price'), cost: $state);
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
                            ])
                            ->columns(2)
                            ->collapsible(),
                        Forms\Components\Section::make(__('Controle de Estoque'))
                            ->description(__('Informe a quantidade disponível para venda. Você tem opção de receber um aviso quando chegar ao estoque mínimo.'))
                            ->schema([
                                Forms\Components\TextInput::make('default_variant.sku')
                                    ->label(__('SKU (Unidade de manutenção de estoque)'))
                                    // ->unique(ProductVariantItem::class, 'sku', ignoreRecord: true)
                                    ->minLength(2)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('default_variant.barcode')
                                    ->label(__('Código de barras (ISBN, UPC, GTIN etc.)'))
                                    // ->unique(ProductVariantItem::class, 'barcode', ignoreRecord: true)
                                    ->minLength(2)
                                    ->maxLength(255),
                                Forms\Components\Checkbox::make('default_variant.inventory_management')
                                    ->label(__('Acompanhar quantidade'))
                                    ->default(true)
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\Checkbox::make('default_variant.inventory_out_allowed')
                                    ->label(__('Continuar vendendo mesmo sem estoque'))
                                    ->helperText(__('Permite que os clientes comprem o item quando ele estiver fora de estoque (igual ou inferior a zero).'))
                                    ->hidden(
                                        fn (callable $get): bool =>
                                        !$get('default_variant.inventory_management')
                                    )
                                    ->columnSpanFull(),
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 3,
                                            'sm'      => 1,
                                        ])
                                            ->schema([
                                                Forms\Components\TextInput::make('default_variant.inventory.available')
                                                    ->numeric()
                                                    ->label(__('Estoque disponível'))
                                                    ->default(0)
                                                    ->live(debounce: 1000)
                                                    ->afterStateUpdated(
                                                        function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                            $inventoryTotal = $service->getInventoryTotal(data: $get('default_variant.inventory'));
                                                            $set('default_variant.inventory.total', $inventoryTotal);
                                                        }
                                                    ),
                                                Forms\Components\TextInput::make('default_variant.inventory.committed')
                                                    ->numeric()
                                                    ->label(__('Comprometido'))
                                                    ->default(0)
                                                    ->live(debounce: 1000)
                                                    ->afterStateUpdated(
                                                        function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                            $inventoryTotal = $service->getInventoryTotal(data: $get('default_variant.inventory'));
                                                            $set('default_variant.inventory.total', $inventoryTotal);
                                                        }
                                                    ),
                                                Forms\Components\TextInput::make('default_variant.inventory.to_receive')
                                                    ->numeric()
                                                    ->label(__('A ser recebido'))
                                                    ->default(0),
                                            ]),
                                        Forms\Components\Fieldset::make(__('Estoque indisponível'))
                                            ->schema([
                                                Forms\Components\TextInput::make('default_variant.inventory.unavailable_damaged')
                                                    ->numeric()
                                                    ->label(__('Danificado'))
                                                    ->default(0)
                                                    ->live(debounce: 1000)
                                                    ->afterStateUpdated(
                                                        function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                            $inventoryTotal = $service->getInventoryTotal(data: $get('default_variant.inventory'));
                                                            $set('default_variant.inventory.total', $inventoryTotal);
                                                        }
                                                    ),
                                                Forms\Components\TextInput::make('default_variant.inventory.unavailable_quality_control')
                                                    ->numeric()
                                                    ->label(__('Controle de qualidade'))
                                                    ->default(0)
                                                    ->live(debounce: 1000)
                                                    ->afterStateUpdated(
                                                        function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                            $inventoryTotal = $service->getInventoryTotal(data: $get('default_variant.inventory'));
                                                            $set('default_variant.inventory.total', $inventoryTotal);
                                                        }
                                                    ),
                                                Forms\Components\TextInput::make('default_variant.inventory.unavailable_safety')
                                                    ->numeric()
                                                    ->label(__('Estoque de segurança'))
                                                    ->default(0),
                                                Forms\Components\TextInput::make('default_variant.inventory.unavailable_other')
                                                    ->numeric()
                                                    ->label(__('Outro'))
                                                    ->default(0)
                                                    ->live(debounce: 1000)
                                                    ->afterStateUpdated(
                                                        function (ProductVariantItemService $service, callable $set, callable $get): void {
                                                            $inventoryTotal = $service->getInventoryTotal(data: $get('default_variant.inventory'));
                                                            $set('default_variant.inventory.total', $inventoryTotal);
                                                        }
                                                    ),
                                            ])
                                            ->columns(4),
                                    ])
                                    ->hidden(
                                        fn (callable $get, string $operation): bool =>
                                        !$get('default_variant.inventory_management') || $operation === 'create'
                                    )
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('default_variant.inventory.total')
                                    ->numeric()
                                    ->label(__('Total em estoque'))
                                    ->helperText(__('Estoque completo que você tem em um local, incluindo a quantidade de estoque comprometido, indisponível e disponível.'))
                                    ->default(0)
                                    ->disabled()
                                    ->hidden(
                                        fn (callable $get, string $operation): bool =>
                                        !$get('default_variant.inventory_management') || $operation === 'create'
                                    ),
                                Forms\Components\TextInput::make('default_variant.inventory_quantity')
                                    ->numeric()
                                    ->label(__('Quantidade em estoque'))
                                    ->mask(9999999)
                                    ->hidden(
                                        fn (callable $get, string $operation): bool =>
                                        !$get('default_variant.inventory_management') || $operation === 'edit'
                                    ),
                                Forms\Components\TextInput::make('default_variant.inventory_security_alert')
                                    ->numeric()
                                    ->label(__('Alerta de segurança'))
                                    ->helperText(__('Estoque limite para seus produtos, que lhe alerta se o produto estará em breve fora de estoque.'))
                                    ->mask(9999999)
                                    ->hidden(
                                        fn (callable $get): bool =>
                                        !$get('default_variant.inventory_management')
                                    ),
                            ])
                            ->columns(2)
                            ->collapsible(),
                        Forms\Components\Section::make(__('Frete'))
                            ->description(__('Informe o peso e as dimensões da embalagem do produto para ser usado no cálculo do frete.'))
                            ->schema([
                                Forms\Components\Checkbox::make('default_variant.requires_shipping')
                                    ->label(__('Este produto exige frete'))
                                    ->default(true)
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make([
                                    'default' => 2,
                                    'lg'      => 4,
                                ])
                                    ->schema([
                                        Forms\Components\TextInput::make('default_variant.weight')
                                            ->label(__('Peso'))
                                            ->numeric()
                                            ->mask(9999999)
                                            ->suffix(__('gramas')),
                                        Forms\Components\TextInput::make('default_variant.dimensions.height')
                                            ->label(__('Altura'))
                                            ->numeric()
                                            ->mask(9999999)
                                            ->suffix(__('cm')),
                                        Forms\Components\TextInput::make('default_variant.dimensions.width')
                                            ->label(__('Largura'))
                                            ->numeric()
                                            ->mask(9999999)
                                            ->suffix(__('cm')),
                                        Forms\Components\TextInput::make('default_variant.dimensions.length')
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
                                        !$get('default_variant.requires_shipping')
                                    ),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ])
                    ->hidden(
                        fn (string $operation, callable $get): bool =>
                        $operation === 'edit' && $get('has_variants')
                    )
                    ->columnSpanFull(),
                Forms\Components\Section::make(__('Opções de Variantes'))
                    ->description(__('As variantes são adicionadas a um produto que tem mais de uma opção, como tamanho ou cor.'))
                    ->schema([
                        Forms\Components\Checkbox::make('has_variants')
                            ->label(__('Este produto possui variantes'))
                            ->disabled(
                                fn (string $operation, Product $product): bool =>
                                $operation === 'edit' && $product->has_variants
                            )
                            ->live(),
                        Forms\Components\Repeater::make('variants')
                            ->label('')
                            ->relationship(
                                name: 'variantOptions',
                                modifyQueryUsing: fn (ProductVariantItemService $service, Builder $query): Builder =>
                                $service->ignoreDefaultVariantOption($query)
                            )
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome da variante')
                                    ->required()
                                    ->maxLength(255)
                                    ->datalist([
                                        'Tamanho',
                                        'Cor',
                                        'Material',
                                        'Estilo'
                                    ])
                                    ->autocomplete(false),
                                // Forms\Components\TagsInput::make('option_values')
                                //     ->label(__('Opções'))
                                //     ->required()
                                //     ->nestedRecursiveRules([
                                //         'min:1',
                                //         'max:100',
                                //     ]),
                                Forms\Components\Repeater::make('option_values')
                                    ->label(__('Lista de opções'))
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nome da opção')
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->itemLabel(
                                        fn (array $state): ?string =>
                                        $state['name'] ?? null
                                    )
                                    ->addActionLabel(__('Adicionar opção'))
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->collapseAllAction(
                                        fn (Forms\Components\Actions\Action $action) =>
                                        $action->label(__('Minimizar todos'))
                                    )
                                    ->deleteAction(
                                        fn (Forms\Components\Actions\Action $action) =>
                                        $action->requiresConfirmation()
                                    )
                                    ->minItems(1)
                                    ->maxItems(50)
                                    ->grid(3),
                            ])
                            ->itemLabel(
                                fn (array $state): ?string =>
                                $state['name'] ?? null
                            )
                            ->addActionLabel(__('Adicionar variante'))
                            ->defaultItems(1)
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->collapseAllAction(
                                fn (Forms\Components\Actions\Action $action) =>
                                $action->label(__('Minimizar todos'))
                            )
                            ->deleteAction(
                                fn (Forms\Components\Actions\Action $action) =>
                                $action->requiresConfirmation()
                            )
                            ->minItems(1)
                            ->maxItems(3)
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('has_variants')
                            )
                            ->grid(1),
                    ])
                    ->columns(1)
                    ->collapsible(),
                Forms\Components\Section::make(__('Infos. Complementares'))
                    ->description(__('Forneça informações adicionais relevantes sobre o produto.'))
                    ->schema([
                        Forms\Components\Select::make('brand_id')
                            ->label(__('Marca / Fabricante'))
                            ->relationship(
                                name: 'productBrand',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (ProductBrandService $service): Builder =>
                                $service->forceScopeActiveStatus()
                            )
                            ->searchable()
                            ->preload()
                            ->when(
                                auth()->user()->can('Cadastrar [Shop] Marcas'),
                                fn (Forms\Components\Select $component): Forms\Components\Select =>
                                $component->createOptionForm([
                                    Forms\Components\Grid::make(['default' => 2])
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
                                        ]),
                                ])
                            )
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('tags')
                            ->label(__('Tags'))
                            ->helperText(__('As tags são usadas para filtragem e busca. Um produto pode ter até 120 tags.'))
                            ->nestedRecursiveRules([
                                // 'min:1',
                                'max:120',
                            ])
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('body')
                            ->label(__('Descrição completa'))
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('pages')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                        Forms\Components\Fieldset::make(__('Publicação nos canais'))
                            ->schema([
                                Forms\Components\Checkbox::make('publish_on.point_of_sale')
                                    ->label(__('Ponto de venda'))
                                    ->default(true),
                                Forms\Components\Checkbox::make('publish_on.e_commerce')
                                    ->label(__('Loja virtual'))
                                    ->live()
                                    ->default(true),
                            ])
                            ->columns(4),
                        Forms\Components\Fieldset::make(__('Otimização para motores de busca (SEO)'))
                            ->relationship(name: 'cmsPost')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label(__('Título SEO'))
                                    ->helperText('55 - 60 caracteres')
                                    ->minLength(2)
                                    ->maxLength(60)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label(__('Descrição SEO'))
                                    ->rows(4)
                                    ->helperText('152 - 155 caracteres')
                                    ->minLength(2)
                                    ->maxLength(155)
                                    ->columnSpanFull(),
                                // Forms\Components\TagsInput::make('meta_keywords')
                                //     ->label(__('Palavras chave'))
                                //     ->columnSpanFull(),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('publish_on.e_commerce')
                            ),
                        Forms\Components\Grid::make(['default' => 3])
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->label(__('Ordem'))
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(100),
                                Forms\Components\Toggle::make('featured')
                                    ->label(__('Destaque?'))
                                    ->default(true)
                                    ->inline(false),
                                Forms\Components\Toggle::make('comment')
                                    ->label(__('Comentário?'))
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('publish_on.e_commerce')
                            ),
                        Forms\Components\Fieldset::make(__('Datas da postagem'))
                            ->schema([
                                Forms\Components\DateTimePicker::make('publish_at')
                                    ->label(__('Dt. publicação'))
                                    ->displayFormat('d/m/Y H:i')
                                    ->seconds(false)
                                    ->default(now())
                                    ->required(),
                                Forms\Components\DateTimePicker::make('expiration_at')
                                    ->label(__('Dt. expiração'))
                                    ->displayFormat('d/m/Y H:i')
                                    ->seconds(false)
                                    ->minDate(
                                        fn (callable $get): string =>
                                        $get('publish_at')
                                    ),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('publish_on.e_commerce')
                            ),
                        Forms\Components\Group::make()
                            ->relationship(name: 'cmsPost')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label(__('Status'))
                                    ->options(DefaultPostStatus::asSelectArray())
                                    ->default(1)
                                    ->required()
                                    ->in(DefaultPostStatus::getValues())
                                    ->native(false),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !$get('publish_on.e_commerce')
                            ),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->label('')
                    ->collection('images')
                    ->conversion('thumb')
                    ->size(45)
                    ->limit(1)
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Produto'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productCategory.name')
                    ->label(__('Categoria'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('productBrand.name')
                    ->label(__('Marca / Fabricante'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('ref_sku')
                    ->label(__('SKU Ref.'))
                    ->searchable(
                        query: fn (ProductService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchBySku(query: $query, search: $search)
                    ),
                Tables\Columns\TextColumn::make('ref_price')
                    ->label(__('Preço Ref. (R$)')),
                Tables\Columns\TextColumn::make('available_inventory')
                    ->label(__('Estoque disponível')),
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
            // ->reorderable('order')
            ->defaultSort(
                fn (PostService $service, Builder $query): Builder =>
                $service->tableDefaultSort(query: $query)
            )
            ->filters([
                Tables\Filters\SelectFilter::make('productCategory')
                    ->label(__('Categorias'))
                    ->relationship(
                        name: 'productCategory',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (ProductCategoryService $service, Builder $query): Builder =>
                        $service->getActiveCategories(query: $query)
                    )
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('productBrand')
                    ->label(__('Marcas / Fabricantes'))
                    ->relationship(
                        name: 'productBrand',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (ProductBrandService $service, Builder $query): Builder =>
                        $service->getActiveBrands(query: $query)
                    )
                    ->multiple()
                    ->preload(),
                Tables\Filters\Filter::make('created_at')
                    ->label(__('Cadastro'))
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('Cadastro de'))
                            ->live(debounce: 500)
                            ->afterStateUpdated(
                                function (callable $get, callable $set, ?string $state): void {
                                    if (empty($get('created_until'))) {
                                        $set('created_until', $state);
                                    }
                                }
                            ),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('Cadastro até'))
                            ->live(debounce: 500)
                            ->afterStateUpdated(
                                function (callable $get, callable $set, ?string $state): void {
                                    if (empty($get('created_from'))) {
                                        $set('created_from', $state);
                                    }
                                }
                            ),
                    ])
                    ->query(
                        fn (ProductService $service, Builder $query, array $data): Builder =>
                        $service->tableFilterByCreatedAt(query: $query, data: $data)
                    ),
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
                    ->label(__('Produto')),
                Infolists\Components\TextEntry::make('slug')
                    ->label(__('Slug')),
                // Infolists\Components\TextEntry::make('cmsPost.display_status')
                //     ->label(__('Status')),
                // Infolists\Components\TextEntry::make('publish_at')
                //     ->label(__('Dt. publicação'))
                //     ->dateTime('d/m/Y H:i'),
                // Infolists\Components\TextEntry::make('expiration_at')
                //     ->label(__('Dt. expiração'))
                //     ->dateTime('d/m/Y H:i'),
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
            RelationManagers\VariantItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
