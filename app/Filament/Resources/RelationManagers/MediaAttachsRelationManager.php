<?php

namespace App\Filament\Resources\RelationManagers;

use App\Services\MediaAttachService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Support;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaAttachsRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $title = 'Anexos';

    protected static ?string $modelLabel = 'Anexo';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Nome'))
                    ->required()
                    ->minLength(2)
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file_name')
                    ->label(__('Anexar arquivo(s)'))
                    ->helperText(__('Máx. 25 mb.'))
                    ->multiple(
                        fn (string $operation): bool =>
                        $operation === 'create'
                    )
                    ->disk('public')
                    ->directory('attachments')
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file, callable $get): string =>
                        (string) str('-' . md5(uniqid()) . '-' . time() . '.' . $file->extension())
                            ->prepend(Str::slug($get('name'))),
                    )
                    // ->afterStateUpdated(
                    //     function (callable $set, UploadedFile $state): void {
                    //         $set('mime_type', $state->getMimeType());
                    //         $set('size', $state->getSize());
                    //     }
                    // )
                    ->required()
                    ->maxSize(25600)
                    ->downloadable()
                    ->columnSpanFull(),
                // Forms\Components\Hidden::make('mime_type'),
                // Forms\Components\Hidden::make('size'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(
                fn (Builder $query): Builder =>
                $query->where('collection_name', 'attachments')
            )
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nome'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label(__('Mime'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_column')
                    ->label(__('Ordem'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Cadastro'))
                    ->dateTime('d/m/Y H:i')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Últ. atualização'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->reorderable('order_column')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('download')
                            ->icon('heroicon-s-arrow-down-tray')
                            ->action(
                                fn (Media $media): StreamedResponse =>
                                Storage::disk('public')
                                    ->download($media->file_name)
                            ),
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make()
                            ->mutateFormDataUsing(
                                fn (MediaAttachService $service, Media $media, array $data): array =>
                                $service->mutateFormDataToEdit(media: $media, data: $data)
                            ),
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
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(
                        fn (MediaAttachService $service, array $data): array =>
                        $service->mutateFormDataToCreate(ownerRecord: $this->ownerRecord, data: $data)
                    )
                    ->using(
                        function (array $data, string $model): Model {
                            foreach ($data as $item) {
                                $model::create($item);
                            }

                            return $this->ownerRecord;
                        }
                    ),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name')
                    ->label(__('Nome')),
                Infolists\Components\TextEntry::make('mime_type')
                    ->label(__('Mime')),
                Infolists\Components\TextEntry::make('size')
                    ->label(__('Tamanho do arquivo')),
                Infolists\Components\TextEntry::make('order_column')
                    ->label(__('Ordem')),
                Infolists\Components\TextEntry::make('file_name')
                    ->label('Nome do arquivo')
                    ->columnSpanFull(),
                Infolists\Components\ImageEntry::make('file_name')
                    ->label('')
                    ->hidden(
                        fn (Media $media): bool =>
                        !in_array(
                            Storage::disk('public')
                                ->mimeType($media->file_name),
                            ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml']
                        )
                    )
                    ->columnSpanFull(),
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
            return !in_array('attachments', $ownerRecord->settings) ? false : true;
        }

        return true;
    }
}
