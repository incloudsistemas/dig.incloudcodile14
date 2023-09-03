<?php

namespace App\Filament\Resources\Cms\RelationManagers;

use App\Enums\Cms\DefaultPostStatus;
use App\Enums\Cms\PostSliderRole;
use App\Services\Cms\PostSliderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class PostSlidersRelationManager extends RelationManager
{
    protected static string $relationship = 'sliders';

    protected static ?string $title = 'Sliders';

    protected static ?string $modelLabel = 'Slider';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label(__('Tipo do slider'))
                            ->options(PostSliderRole::asSelectArray())
                            ->default(1)
                            ->disabled(
                                fn (string $operation): bool =>
                                $operation === 'edit'
                            )
                            ->required()
                            ->in(PostSliderRole::getValues())
                            ->native(false)
                            ->live()
                            ->columnSpan(3),
                        Forms\Components\Toggle::make('settings.hide_text')
                            ->label(__('Ocultar texto?'))
                            ->inline(false)
                            ->live(),
                    ]),
                Forms\Components\TextInput::make('title')
                    ->label(__('Título'))
                    ->required()
                    ->minLength(2)
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('subtitle')
                    ->label(__('Subtítulo'))
                    ->minLength(2)
                    ->maxLength(255)
                    ->hidden(
                        fn (callable $get): bool =>
                        $get('settings.hide_text') === true
                    )
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('body')
                    ->label(__('Conteúdo'))
                    ->rows(4)
                    ->minLength(2)
                    ->maxLength(65535)
                    ->hidden(
                        fn (callable $get): bool =>
                        $get('settings.hide_text') === true
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
                        $get('settings.hide_text') === true
                    )
                    ->columns(4),
                Forms\Components\TextInput::make('embed_video')
                    ->label(__('Youtube vídeo'))
                    ->prefix('.../watch?v=')
                    ->helperText(new HtmlString('https://youtube.com/watch?v=<span class="font-bold">kJQP7kiw5Fk</span>'))
                    ->required(
                        fn (callable $get): bool =>
                        (int) $get('role') === 3
                    )
                    ->maxLength(255)
                    ->hidden(
                        fn (callable $get): bool =>
                        (int) $get('role') !== 3
                    )
                    ->columnSpanFull(),
                Forms\Components\SpatieMediaLibraryFileUpload::make('video')
                    ->label(__('Vídeo destaque'))
                    ->helperText(__('Tipo de arquivo permitido: .mp4. // Máx. 25 mb.'))
                    ->collection('video')
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file, callable $get): string =>
                        (string) str('-' . md5(uniqid()) . '-' . time() . '.' . $file->extension())
                            ->prepend(Str::slug($get('name'))),
                    )
                    ->required(
                        fn (callable $get): bool =>
                        (int) $get('role') === 2
                    )
                    ->acceptedFileTypes(['video/mp4'])
                    ->maxSize(25600)
                    ->downloadable()
                    ->hidden(
                        fn (callable $get): bool =>
                        (int) $get('role') !== 2
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
                            ->prepend(Str::slug($get('name'))),
                    )
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->required(
                        fn (callable $get): bool =>
                        (int) $get('role') === 1
                    )
                    ->maxSize(5120)
                    ->downloadable(),
                Forms\Components\Fieldset::make(__('Configs. de estilo'))
                    ->schema([
                        Forms\Components\Select::make('settings.style')
                            ->label(__('Contraste'))
                            ->options([
                                'dark'  => 'Escuro',
                                'light' => 'Claro',
                                'none'  => 'Nenhum'
                            ])
                            ->default('dark')
                            ->selectablePlaceholder(false)
                            ->native(false),
                        Forms\Components\Select::make('settings.text_indent')
                            ->label(__('Identação do texto'))
                            ->options([
                                'left'   => 'Esquerda',
                                'right'  => 'Direita',
                                'center' => 'Centro'
                            ])
                            ->default('left')
                            ->selectablePlaceholder(false)
                            ->native(false)
                            ->hidden(
                                fn (callable $get): bool =>
                                $get('settings.hide_text') === true
                            ),
                        Forms\Components\ColorPicker::make('settings.text_color')
                            ->label(__('Cor do texto (hexadecimal)'))
                            ->hidden(
                                fn (callable $get): bool =>
                                $get('settings.hide_text') === true
                            ),
                    ])
                    ->columns(3),
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
                    ]),
                // Forms\Components\TextInput::make('order')
                //     ->numeric()
                //     ->label(__('Ordem'))
                //     ->default(1)
                //     ->required()
                //     ->minValue(1)
                //     ->maxValue(100),
                Forms\Components\Select::make('status')
                    ->label(__('Status'))
                    ->options(DefaultPostStatus::asSelectArray())
                    ->default(1)
                    ->required()
                    ->in(DefaultPostStatus::getValues())
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->striped()
            ->columns([
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
                Tables\Columns\TextColumn::make('display_role')
                    ->label(__('Tipo'))
                    ->searchable(
                        query: fn (PostSliderService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchByRole(query: $query, search: $search)
                    )
                    ->sortable(
                        query: fn (PostSliderService $service, Builder $query, string $direction): Builder =>
                        $service->tableSortByRole(query: $query, direction: $direction)
                    ),
                Tables\Columns\TextColumn::make('order')
                    ->label(__('Ordem'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(
                        fn (string $state): string =>
                        DefaultPostStatus::getColorByDescription(statusDesc: $state)
                    )
                    ->searchable(
                        query: fn (PostSliderService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchByStatus(query: $query, search: $search)
                    )
                    ->sortable(
                        query: fn (PostSliderService $service, Builder $query, string $direction): Builder =>
                        $service->tableSortByStatus(query: $query, direction: $direction)
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
            ->reorderable('order')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label(__('Tipo'))
                    ->multiple()
                    ->options(PostSliderRole::asSelectArray()),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->multiple()
                    ->options(DefaultPostStatus::asSelectArray()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('display_role')
                    ->label(__('Tipo do slider')),
                Infolists\Components\TextEntry::make('title')
                    ->label(__('Título')),
                Infolists\Components\TextEntry::make('display_publish_at')
                    ->label(__('Dt. publicação')),
                // Infolists\Components\TextEntry::make('display_expiration_at')
                //     ->label(__('Dt. expiração')),
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

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if ($ownerRecord->getTable() === 'cms_pages') {
            return !in_array('sliders', $ownerRecord->settings) ? false : true;
        }

        return true;
    }
}
