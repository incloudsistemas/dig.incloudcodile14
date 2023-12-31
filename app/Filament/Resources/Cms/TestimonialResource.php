<?php

namespace App\Filament\Resources\Cms;

use App\Enums\Cms\DefaultPostStatus;
use App\Enums\Cms\TestimonialRole;
use App\Filament\Resources\Cms\TestimonialResource\Pages;
use App\Filament\Resources\Cms\TestimonialResource\RelationManagers;
use App\Models\Cms\Testimonial;
use App\Services\Cms\PostService;
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

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Depoimento';

    protected static ?string $navigationGroup = 'CMS & Marketing';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('role')
                    ->label(__('Tipo do depoimento'))
                    ->options(TestimonialRole::asSelectArray())
                    ->default(1)
                    ->disabled(
                        fn (string $operation): bool =>
                        $operation === 'edit'
                    )
                    ->required()
                    ->in(TestimonialRole::getValues())
                    ->native(false)
                    ->live()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('customer_name')
                    ->label(__('Nome do cliente'))
                    ->required()
                    ->minLength(2)
                    ->maxLength(255)
                    ->live(debounce: 1000)
                    ->afterStateUpdated(
                        function (callable $set, ?string $state): void {
                            $set('title', $state);
                            $set('slug', Str::slug($state));
                        }
                    ),
                Forms\Components\Hidden::make('title'),
                Forms\Components\TextInput::make('slug')
                    ->label(__('Slug'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('occupation')
                    ->label(__('Cargo'))
                    ->minLength(2)
                    ->maxLength(255),
                Forms\Components\TextInput::make('company')
                    ->label(__('Empresa'))
                    ->minLength(2)
                    ->maxLength(255),
                Forms\Components\TextInput::make('excerpt')
                    ->label(__('Chamada'))
                    ->minLength(2)
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('body')
                    ->label(__('Depoimento'))
                    ->rows(4)
                    ->required(
                        fn (callable $get): bool =>
                        in_array($get('role'), [1,])
                    )
                    ->minLength(2)
                    ->maxLength(65535)
                    ->hidden(
                        fn (callable $get): bool =>
                        in_array($get('role'), [2, 3])
                    )
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('embed_video')
                    ->label(__('Youtube vídeo'))
                    ->prefix('.../watch?v=')
                    ->helperText(new HtmlString('https://youtube.com/watch?v=<span class="font-bold">kJQP7kiw5Fk</span>'))
                    ->required(
                        fn (callable $get): bool =>
                        in_array($get('role'), [3,]) && empty($get('video'))
                    )
                    ->maxLength(255)
                    ->hidden(
                        fn (callable $get): bool =>
                        !in_array($get('role'), [3,])
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
                        in_array($get('role'), [3,]) && empty($get('embed_video'))
                    )
                    ->acceptedFileTypes(['video/mp4'])
                    ->maxSize(25600)
                    ->downloadable()
                    ->hidden(
                        fn (callable $get): bool =>
                        !in_array($get('role'), [3,])
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
                    ->required(
                        fn (callable $get): bool =>
                        in_array($get('role'), [2,])
                    )
                    ->maxSize(5120)
                    ->downloadable(),
                // Forms\Components\SpatieMediaLibraryFileUpload::make('company-logo')
                //     ->label(__('Logo da empresa'))
                //     ->helperText(__('Tipos de arquivo permitidos: .png, .jpg, .jpeg, .gif. // Máx. 1920x1080px // 5 mb.'))
                //     ->collection('company-logo')
                //     ->image()
                //     ->responsiveImages()
                //     ->getUploadedFileNameForStorageUsing(
                //         fn (TemporaryUploadedFile $file, callable $get): string =>
                //         (string) str('-' . md5(uniqid()) . '-' . time() . '.' . $file->extension())
                //             ->prepend($get('slug')),
                //     )
                //     ->imageResizeMode('contain')
                //     ->imageResizeTargetWidth('1920')
                //     ->imageResizeTargetHeight('1080')
                //     ->imageResizeUpscale(false)
                //     ->maxSize(5120)
                //     ->downloadable(),
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
            ]);
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
                // Tables\Columns\TextColumn::make('cmsPost.postCategories.name')
                //     ->label(__('Categorias'))
                //     ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->label(__('Ordem'))
                    ->sortable(),
                // Tables\Columns\TextColumn::make('cmsPost.owner.name')
                //     ->label(__('Autor'))
                //     ->searchable()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: false),
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
                        $service->tableSortByStatus(postableType: 'cms_portfolio_posts', query: $query, direction: $direction)
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
                // Tables\Filters\SelectFilter::make('postCategories')
                //     ->label(__('Categorias'))
                //     ->options(
                //         fn (PostService $service): array =>
                //         $service->tableFilterGetOptionsByCategories(postableType: 'cms_portfolio_posts')
                //     )
                //     ->query(
                //         fn (PostService $service, Builder $query, array $data): Builder =>
                //         $service->tableFilterGetQueryByCategories(query: $query, data: $data)
                //     )
                //     ->multiple(),
                // Tables\Filters\SelectFilter::make('owners')
                //     ->label(__('Autores'))
                //     ->options(
                //         fn (PostService $service): array =>
                //         $service->tableFilterGetOptionsByOwners(postableType: 'cms_portfolio_posts')
                //     )
                //     ->query(
                //         fn (PostService $service, Builder $query, array $data): Builder =>
                //         $service->tableFilterGetQueryByOwners(query: $query, data: $data)
                //     )
                //     ->multiple(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTestimonials::route('/'),
        ];
    }
}
