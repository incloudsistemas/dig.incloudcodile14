<?php

namespace App\Filament\Resources\Cms;

use App\Enums\Cms\BlogRole;
use App\Enums\Cms\DefaultPostStatus;
use App\Filament\Resources\Cms\BlogPostResource\Pages;
use App\Filament\Resources\Cms\BlogPostResource\RelationManagers;
use App\Models\Cms\BlogPost;
use App\Services\Cms\BlogPostService;
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

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Postagem';

    protected static ?string $pluralModelLabel = 'Blog';

    protected static ?string $navigationGroup = 'CMS & Marketing';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Blog';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Infos. Gerais'))
                    ->description(__('Visão geral e informações fundamentais sobre a postagem.'))
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label(__('Tipo da postagem'))
                            ->options(BlogRole::asSelectArray())
                            ->default(1)
                            ->disabled(
                                fn (string $operation): bool =>
                                $operation === 'edit'
                            )
                            ->required()
                            ->in(BlogRole::getValues())
                            ->native(false)
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->label(__('Título'))
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
                        Forms\Components\Group::make()
                            ->relationship(name: 'cmsPost')
                            ->schema([
                                static::getCategoriesFormField()
                            ])
                            ->visibleOn('create')
                            ->columnSpanFull(),
                        Forms\Components\Group::make()
                            ->schema([
                                static::getCategoriesFormField()
                            ])
                            ->visibleOn('edit')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('subtitle')
                            ->label(__('Subtítulo'))
                            ->minLength(2)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('excerpt')
                            ->label(__('Resumo/Chamada'))
                            ->rows(4)
                            ->minLength(2)
                            ->maxLength(65535)
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
                                !in_array($get('role'), [1, 3, 4])
                            )
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('url')
                            ->label(__('URL'))
                            ->url()
                            // ->prefix('https://')
                            ->helperText('https://...')
                            ->required(
                                fn (callable $get): bool =>
                                in_array($get('role'), [2,])
                            )
                            ->maxLength(255)
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array($get('role'), [2,])
                            )
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('embed_video')
                            ->label(__('Youtube vídeo'))
                            ->prefix('.../watch?v=')
                            ->helperText(new HtmlString('https://youtube.com/watch?v=<span class="font-bold">kJQP7kiw5Fk</span>'))
                            ->required(
                                fn (callable $get): bool =>
                                in_array($get('role'), [4,]) && empty($get('video'))
                            )
                            ->maxLength(255)
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array($get('role'), [3, 4])
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
                            ->required(
                                fn (callable $get): bool =>
                                in_array($get('role'), [4,]) && empty($get('embed_video'))
                            )
                            ->acceptedFileTypes(['video/mp4'])
                            ->maxSize(25600)
                            ->downloadable()
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array($get('role'), [3, 4])
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
                            ->imageResizeUpscale(false)
                            ->maxSize(5120)
                            ->downloadable(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make(__('Galeria de Imagens e Vídeos'))
                    ->description(__('Adicione e gerencie as imagens e vídeos da postagem.'))
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
                    ->hidden(
                        fn (callable $get): bool =>
                        !in_array($get('role'), [3,])
                    )
                    ->collapsible(),
                Forms\Components\Section::make(__('Infos. Complementares'))
                    ->description(__('Forneça informações adicionais relevantes sobre a postagem.'))
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label(__('Tags'))
                            ->helperText(__('As tags são usadas para filtragem e busca. Uma postagem pode ter até 120 tags.'))
                            ->nestedRecursiveRules([
                                // 'min:1',
                                'max:120',
                            ])
                            ->columnSpanFull(),
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
                                //     // ->separator(',')
                                //     ->columnSpanFull(),
                            ])
                            ->hidden(
                                fn (callable $get): bool =>
                                !in_array($get('role'), [1, 3, 4])
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
                                !in_array($get('role'), [1, 3, 4])
                            )
                            ->columnSpanFull(),
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
                                    ->inline(false)
                                    ->hidden(
                                        fn (callable $get): bool =>
                                        !in_array($get('role'), [1, 3, 4])
                                    ),
                            ]),
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
                            ]),
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
                            ]),
                    ])
                    ->columns(2)
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
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->label('')
                    ->collection('image')
                    ->conversion('thumb')
                    ->size(45)
                    ->limit(1)
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Título'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_role')
                    ->label(__('Tipo'))
                    ->searchable(
                        query: fn (BlogPostService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchByRole(query: $query, search: $search)
                    )
                    ->sortable(
                        query: fn (BlogPostService $service, Builder $query, string $direction): Builder =>
                        $service->tableSortByRole(query: $query, direction: $direction)
                    ),
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
                    ->toggleable(isToggledHiddenByDefault: false),
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
                        $service->tableSortByStatus(postableType: 'cms_blog_posts', query: $query, direction: $direction)
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
            ])
            // ->reorderable('order')
            ->defaultSort(
                fn (PostService $service, Builder $query): Builder =>
                $service->tableDefaultSort(query: $query)
            )
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label(__('Tipos'))
                    ->options(BlogRole::asSelectArray())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('postCategories')
                    ->label(__('Categorias'))
                    ->options(
                        fn (PostService $service): array =>
                        $service->tableFilterGetOptionsByCategories(postableType: 'cms_blog_posts')
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
                        $service->tableFilterGetOptionsByOwners(postableType: 'cms_blog_posts')
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
                Infolists\Components\TextEntry::make('display_role')
                    ->label(__('Tipo da postagem')),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
