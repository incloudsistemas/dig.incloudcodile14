<?php

namespace App\Filament\Resources\Cms;

use App\Enums\Cms\DefaultPostStatus;
use App\Filament\Resources\Cms\PageResource\Pages;
use App\Filament\Resources\Cms\PageResource\RelationManagers\SubpagesRelationManager;
use App\Filament\Resources\Cms\RelationManagers\PostSlidersRelationManager;
use App\Filament\Resources\Cms\RelationManagers\PostSubcontentsAccordionsRelationManager;
use App\Filament\Resources\Cms\RelationManagers\PostSubcontentsTabsRelationManager;
use App\Filament\Resources\RelationManagers\MediaAttachsRelationManager;
use App\Models\Cms\Page;
use App\Services\Cms\PageService;
use App\Services\Cms\PostCategoryService;
use App\Services\Cms\PostService;
use App\Services\UserService;
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
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Página';

    protected static ?string $navigationGroup = 'CMS & Marketing';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-window';
    // protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Infos. Gerais'))
                    ->description(__('Visão geral e informações fundamentais sobre a página.'))
                    ->schema([
                        Forms\Components\Select::make('page_id')
                            ->label(__('Página parental'))
                            ->relationship(
                                name: 'mainPage',
                                titleAttribute: 'title',
                                modifyQueryUsing: fn (PageService $service, Builder $query, Page $page): Builder =>
                                $service->getMainPages(query: $query, page: $page)
                            )
                            ->default($_GET['main-page'] ?? null)
                            ->searchable()
                            ->preload()
                            ->disabled(
                                fn (): bool =>
                                isset($_GET['main-page']) || !auth()->user()->can('Cadastrar [Cms] Páginas')
                            )
                            ->dehydrated()
                            ->hidden(
                                fn (Page $page, ?string $state): bool =>
                                (empty($state) && !auth()->user()->can('Cadastrar [Cms] Páginas')) || $page->subpages->count() > 0
                            )
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->label(__('Título'))
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->live(debounce: 1000)
                            ->afterStateUpdated(
                                fn (callable $set, ?string $state): ?string =>
                                auth()->user()->can('Cadastrar [Cms] Páginas')
                                    ? $set('slug', Str::slug($state))
                                    : null
                            )
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled(
                                fn (): bool =>
                                !auth()->user()->can('Cadastrar [Cms] Páginas')
                            )
                            ->columnSpanFull(),
                        Forms\Components\Group::make()
                            ->relationship(name: 'cmsPost')
                            ->schema([
                                static::getCategoriesFormField()
                            ])
                            ->visibleOn('create')
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('categories', $get('settings'))
                            )
                            ->columnSpanFull(),
                        Forms\Components\Group::make()
                            ->schema([
                                static::getCategoriesFormField()
                            ])
                            ->visibleOn('edit')
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('categories', $get('settings'))
                            )
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('subtitle')
                            ->label(__('Subtítulo'))
                            ->minLength(2)
                            ->maxLength(255)
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('subtitle', $get('settings'))
                            )
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('excerpt')
                            ->label(__('Resumo/Chamada'))
                            ->rows(4)
                            ->minLength(2)
                            ->maxLength(65535)
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('excerpt', $get('settings'))
                            )
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('body')
                            ->label(__('Conteúdo'))
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
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('body', $get('settings'))
                            )
                            ->columnSpanFull(),
                        Forms\Components\Fieldset::make(__('Chamada para ação (CTA)'))
                            ->schema([
                                Forms\Components\TextInput::make('cta.url')
                                    ->label(__('URL'))
                                    ->url()
                                    ->helperText('https://...')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('cta.call')
                                    ->label(__('Chamada'))
                                    ->helperText(__('Ex: Saiba mais!')),
                                Forms\Components\Select::make('cta.target')
                                    ->options([
                                        '_self' => 'Mesma janela',
                                        '_blank' => 'Nova janela',
                                    ])
                                    ->label(__('Alvo'))
                                    ->default('_self')
                                    ->selectablePlaceholder(false)
                                    ->native(false),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('cta', $get('settings'))
                            )
                            ->columns(4),
                        Forms\Components\TextInput::make('url')
                            ->label(__('URL'))
                            ->url()
                            // ->prefix('https://')
                            ->helperText('https://...')
                            ->maxLength(255)
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('url', $get('settings'))
                            )
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('embed_video')
                            ->label(__('Youtube vídeo'))
                            ->prefix('.../watch?v=')
                            ->helperText(new HtmlString('https://youtube.com/watch?v=<span class="font-bold">kJQP7kiw5Fk</span>'))
                            ->maxLength(255)
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('embed_video', $get('settings'))
                            ),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('video')
                            ->label(__('Vídeo destaque'))
                            ->helperText(__('Tipo de arquivo permitido: .mp4. // Máx. 25 mb.'))
                            ->collection('video')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file, callable $get): string =>
                                (string) str('-' . md5(uniqid()) . '-' . time() . '.' . $file->extension())
                                    ->prepend($get('slug')),
                            )
                            ->acceptedFileTypes(['video/mp4'])
                            ->maxSize(25600)
                            ->downloadable()
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('video', $get('settings'))
                            ),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('Imagem destaque'))
                            ->helperText(__('Tipos de arquivo permitidos: .png, .jpg, .jpeg, .gif. // Máx. 1920x1080px // 5 mb.'))
                            ->collection('image')
                            ->image()
                            ->responsiveImages()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file, callable $get): string =>
                                (string) str('-' . md5(uniqid()) . '-' . time() . '.' . $file->extension())
                                    ->prepend($get('slug')),
                            )
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->maxSize(5120)
                            ->downloadable()
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('image', $get('settings'))
                            ),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make(__('Infos. Complementares'))
                    ->description(__('Forneça informações adicionais relevantes sobre a página.'))
                    ->schema([
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
                                Forms\Components\TagsInput::make('meta_keywords')
                                    ->label(__('Palavras chave'))
                                    // ->separator(',')
                                    ->columnSpanFull(),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('seo', $get('settings'))
                            ),
                        Forms\Components\Group::make()
                            ->relationship(name: 'cmsPost')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label(__('Autor'))
                                    ->relationship(
                                        name: 'owner',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (UserService $servive): Builder =>
                                        $servive->forceScopeActiveStatus()
                                    )
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('user_id', $get('settings'))
                            )
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(['default' => 3])
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->numeric()
                                    ->label(__('Ordem'))
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->hidden(
                                        fn (callable $get): bool =>
                                        !in_array('order', $get('settings'))
                                    ),
                                Forms\Components\Toggle::make('featured')
                                    ->label(__('Destaque?'))
                                    ->inline(false)
                                    ->hidden(
                                        fn (callable $get): bool =>
                                        !in_array('featured', $get('settings'))
                                    ),
                                Forms\Components\Toggle::make('comment')
                                    ->label(__('Comentário?'))
                                    ->inline(false)
                                    ->hidden(
                                        fn (callable $get): bool =>
                                        !in_array('comment', $get('settings'))
                                    ),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                empty(array_intersect([
                                    'order',
                                    'featured',
                                    'comment'
                                ], $get('settings') ?? []))
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
                                    )
                                    ->hidden(
                                        fn (callable $get): bool =>
                                        !in_array('expiration_at', $get('settings'))
                                    ),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('publish_at', $get('settings'))
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
                                !in_array('status', $get('settings'))
                            ),
                    ])
                    ->hidden(
                        fn (callable $get): bool =>
                        empty(array_intersect([
                            'seo',
                            'user_id',
                            'order',
                            'featured',
                            'comment',
                            'publish_at',
                            'status'
                        ], $get('settings') ?? []))
                    )
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make(
                    function (callable $get): string {
                        $hasImages = in_array('images', $get('settings'));
                        $hasVideos = in_array('videos', $get('settings'));
                        return ($hasImages && $hasVideos)
                            ? __('Galeria de Imagens e Vídeos')
                            : ($hasImages
                                ? __('Galeria de Imagens')
                                : __('Galeria de Vídeos')
                            );
                    }
                )
                    ->description(
                        function (callable $get): string {
                            $hasImages = in_array('images', $get('settings'));
                            $hasVideos = in_array('videos', $get('settings'));
                            return ($hasImages && $hasVideos)
                                ? __('Adicione e gerencie as imagens e vídeos da página.')
                                : ($hasImages
                                    ? __('Adicione e gerencie as imagens da página.')
                                    : __('Adicione e gerencie os vídeos da página.')
                                );
                        }
                    )
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
                            ->maxSize(5120)
                            ->downloadable()
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('images', $get('settings'))
                            ),
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
                            ->downloadable()
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array('videos', $get('settings'))
                            ),
                    ])
                    ->columns(2)
                    ->hidden(
                        fn (callable $get): bool =>
                        empty(array_intersect(['images', 'videos'], $get('settings') ?? []))
                    )
                    ->collapsible(),
                Forms\Components\Section::make(__('Configs. da página'))
                    ->description(__('Personalize a página com os campos desejados.'))
                    ->schema([
                        Forms\Components\CheckboxList::make('settings')
                            ->label('')
                            ->options([
                                'categories' => 'Categorias',
                                'subtitle' => 'Subtítulo',
                                'excerpt' => 'Resumo',
                                'body' => 'Conteúdo',
                                'cta' => 'CTA',
                                'url' => 'Url',
                                'embed_video' => 'Youtube Vídeo',
                                'video' => 'Vídeo',
                                'image' => 'Imagem',
                                'seo' => 'SEO',
                                'user_id' => 'Autor',
                                'order' => 'Ordem',
                                'featured' => 'Destaque',
                                'comment' => 'Comentário',
                                'publish_at' => 'Data de publicação',
                                'expiration_at' => 'Data de expiração',
                                'status' => 'Status',
                                'images' => 'Galeria de Imagens',
                                'videos' => 'Galeria de Vídeos',
                                'sliders' => 'Sliders',
                                'tabs' => 'Abas',
                                'accordions' => 'Acordeões',
                                'attachments' => 'Anexos',
                            ])
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(4)
                            ->gridDirection('row')
                            ->live(),
                    ])
                    ->hidden(
                        fn (): bool =>
                        !auth()->user()->can('Cadastrar [Cms] Páginas')
                    )
                    ->collapsible(),
            ]);
    }

    public static function getCategoriesFormField(): Forms\Components\Select
    {
        return Forms\Components\Select::make('postCategories')
            ->label(__('Categoria(s)'))
            ->relationship(
                name: 'postCategories',
                titleAttribute: 'name',
                modifyQueryUsing: fn (PostCategoryService $servive): Builder =>
                $servive->forceScopeActiveStatus()
            )
            ->multiple()
            ->searchable()
            ->preload()
            ->when(
                auth()->user()->can('Cadastrar [Cms] Categorias'),
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
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns(static::getTableColumns())
            ->defaultSort(
                fn (PostService $service, Builder $query): Builder =>
                $service->tableDefaultSort(query: $query, publishAtDirection: 'asc')
            )
            ->filters(static::getTableFilters())
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                    ])
                        ->dropdown(false),
                    Tables\Actions\DeleteAction::make()
                        ->after(
                            function (PageService $service, PostService $postService, Page $page)  {
                                $service->deleteSubpagesWhenDeleted($page);
                                $postService->anonymizeUniqueSlugWhenDeleted($page);
                            }
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
                Tables\Actions\CreateAction::make(),
            ])
            ->modifyQueryUsing(
                fn (Builder $query): Builder =>
                $query->whereNull('page_id')
            );
    }

    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                ->label('')
                ->collection('image')
                ->conversion('thumb')
                ->size(45)
                ->circular(),
            Tables\Columns\TextColumn::make('title')
                ->label(__('Título'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('cmsPost.postCategories.name')
                ->label(__('Categorias'))
                ->searchable(),
            Tables\Columns\TextColumn::make('order')
                ->label(__('Ordem'))
                ->sortable(),
            Tables\Columns\TextColumn::make('cmsPost.owner.name')
                ->label(__('Autor'))
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('cmsPost.display_status')
                ->label(__('Status'))
                ->badge()
                ->color(
                    fn (string $state): string =>
                    DefaultPostStatus::getColorByDescription(statusDesc: $state)
                )
                ->searchable(
                    query: fn (PostService $service, Builder $query, string $search): Builder =>
                    $service->tableSearchByStatus(query: $query, search: $search)
                )
                ->sortable(
                    query: fn (PostService $service, Builder $query, string $direction): Builder =>
                    $service->tableSortByStatus(postableType: 'cms_pages', query: $query, direction: $direction)
                ),
            Tables\Columns\TextColumn::make('publish_at')
                ->label(__('Publicação'))
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
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('postCategories')
                ->label(__('Categorias'))
                ->options(
                    fn (PostService $service): array =>
                    $service->tableFilterGetOptionsByCategories(postableType: 'cms_pages')
                )
                ->query(
                    fn (PostService $service, Builder $query, array $data): Builder =>
                    $service->tableFilterGetQueryByCategories(query: $query, data: $data)
                )
                ->multiple(),
            Tables\Filters\SelectFilter::make('owners')
                ->label(__('Autores'))
                ->options(
                    fn (PostService $service): array =>
                    $service->tableFilterGetOptionsByOwners(postableType: 'cms_pages')
                )
                ->query(
                    fn (PostService $service, Builder $query, array $data): Builder =>
                    $service->tableFilterGetQueryByOwners(query: $query, data: $data)
                )
                ->multiple(),
            Tables\Filters\SelectFilter::make('status')
                ->label(__('Status'))
                ->options(DefaultPostStatus::asSelectArray())
                ->query(
                    fn (PostService $service, Builder $query, array $data): Builder =>
                    $service->tableFilterGetQueryByStatuses(query: $query, data: $data)
                )
                ->multiple(),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('title')
                    ->label(__('Título')),
                Infolists\Components\TextEntry::make('slug')
                    ->label(__('Slug')),
                Infolists\Components\TextEntry::make('cmsPost.display_status')
                    ->label(__('Status')),
                Infolists\Components\TextEntry::make('publish_at')
                    ->label(__('Dt. publicação'))
                    ->dateTime('d/m/Y H:i'),
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
            SubpagesRelationManager::class,
            PostSlidersRelationManager::class,
            PostSubcontentsTabsRelationManager::class,
            PostSubcontentsAccordionsRelationManager::class,
            MediaAttachsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit'   => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
