<?php

namespace App\Filament\Resources\Cms;

use App\Enums\Cms\DefaultPostStatus;
use App\Filament\Resources\Cms\PartnerResource\Pages;
use App\Filament\Resources\Cms\PartnerResource\RelationManagers;
use App\Models\Cms\Partner;
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

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Parceiro';

    protected static ?string $navigationGroup = 'CMS & Marketing';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                // Forms\Components\TextInput::make('excerpt')
                //     ->label(__('Chamada'))
                //     ->minLength(2)
                //     ->maxLength(255)
                //     ->columnSpanFull(),
                Forms\Components\Textarea::make('body')
                    ->label(__('Descrição'))
                    ->rows(4)
                    ->minLength(2)
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('url')
                    ->label(__('URL'))
                    ->url()
                    // ->prefix('https://')
                    ->helperText('https://...')
                    ->maxLength(255)
                    ->columnSpanFull(),
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
                    ->required()
                    ->maxSize(5120)
                    ->downloadable(),
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
            'index' => Pages\ManagePartners::route('/'),
        ];
    }
}
