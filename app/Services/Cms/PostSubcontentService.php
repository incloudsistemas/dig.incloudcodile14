<?php

namespace App\Services\Cms;

use App\Enums\Cms\DefaultPostStatus;
use App\Models\Cms\PostSubcontent;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class PostSubcontentService
{
    public function __construct(protected PostSubcontent $subcontent)
    {
        $this->subcontent = $subcontent;
    }

    public static function getForm(Form $form, int $role): Form
    {
        return $form
            ->schema([            
                Forms\Components\Hidden::make('role')
                    ->default($role),
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
                    ->columnSpanFull(),
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
                    ->downloadable(),
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
                    ->downloadable(),
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

    public static function getTable(Table $table, int $role): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(
                fn (Builder $query): Builder =>
                $query->where('role', $role)
            )
            ->striped()
            ->defaultSort(
                fn (PostService $service, Builder $query): Builder =>
                $service->tableDefaultSort(query: $query, orderDirection: 'asc', publishAtDirection: 'asc')
            )
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
                        query: fn (PostSubcontentService $service, Builder $query, string $search): Builder =>
                        $service->tableSearchByStatus(query: $query, search: $search)
                    )
                    ->sortable(
                        query: fn (PostSubcontentService $service, Builder $query, string $direction): Builder =>
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

    public static function getInfoList(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('title')
                    ->label(__('Título')),
                Infolists\Components\TextEntry::make('slug')
                    ->label(__('Slug')),
                Infolists\Components\TextEntry::make('display_status')
                    ->label(__('Status')),
                Infolists\Components\TextEntry::make('display_publish_at')
                    ->label(__('Dt. publicação')),
                // Infolists\Components\TextEntry::make('display_expiration_at')
                //     ->label(__('Dt. expiração')),                
                Infolists\Components\TextEntry::make('created_at')
                    ->label(__('Cadastro'))
                    ->dateTime('d/m/Y H:i'),
                Infolists\Components\TextEntry::make('updated_at')
                    ->label(__('Últ. atualização'))
                    ->dateTime('d/m/Y H:i'),
            ])
            ->columns(3);
    }

    public function tableSearchByStatus(Builder $query, string $search): Builder
    {
        $statuses = DefaultPostStatus::asSelectArray();

        $matchingStatuses = [];
        foreach ($statuses as $index => $status) {
            if (stripos($status, $search) !== false) {
                $matchingStatuses[] = $index;
            }
        }

        if ($matchingStatuses) {
            return $query->whereIn('status', $matchingStatuses);
        }

        return $query;
    }

    public function tableSortByStatus(Builder $query, string $direction): Builder
    {
        $statuses = DefaultPostStatus::asSelectArray();

        $caseParts = [];
        $bindings = [];

        foreach ($statuses as $key => $status) {
            $caseParts[] = "WHEN ? THEN ?";
            $bindings[] = $key;
            $bindings[] = $status;
        }

        $orderByCase = "CASE status " . implode(' ', $caseParts) . " END";

        return $query->orderByRaw("$orderByCase $direction", $bindings);
    }
}
