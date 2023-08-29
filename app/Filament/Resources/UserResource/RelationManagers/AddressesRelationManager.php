<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\ProfileInfos\Uf;
use App\Models\Address;
use App\Services\AddressService;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $title = 'Lista de Endereços';

    protected static ?string $modelLabel = 'Endereço';    

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Tipo de endereço'))
                    ->helperText(__('Nome identificador. Ex: Casa, Trabalho...'))
                    ->maxLength(255)
                    ->datalist([
                        'Casa',
                        'Trabalho',
                        'Outros'
                    ])
                    ->autocomplete(false),
                Forms\Components\TextInput::make('zipcode')
                    ->label(__('CEP'))
                    ->mask('99999-999')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('uf')
                    ->label(__('Estado'))
                    ->options(Uf::asSelectArray())
                    ->searchable()
                    ->required()
                    ->in(Uf::getValues())
                    ->native(false),
                Forms\Components\TextInput::make('city')
                    ->label(__('Cidade'))
                    ->required()
                    ->minLength(2)
                    ->maxLength(255),
                Forms\Components\TextInput::make('district')
                    ->label(__('Bairro'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('address_line')
                    ->label(__('Endereço'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('number')
                    ->label(__('Número'))
                    ->minLength(2)
                    ->maxLength(255),
                Forms\Components\TextInput::make('complement')
                    ->label(__('Complemento'))
                    ->helperText(__('Apto / Bloco / Casa'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference')
                    ->label(__('Ponto de referência'))
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Checkbox::make('is_main')
                    ->label(__('Utilizar como endereço principal'))
                    ->default(
                        fn (): bool =>
                        $this->ownerRecord->addresses->count() === 0
                    )
                    ->accepted(
                        fn (): bool =>
                        $this->ownerRecord->addresses->count() === 0
                    )
                    ->disabled(
                        fn (Address $address): bool =>
                        $address->is_main === true
                    )
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordTitle(
                fn (Address $address): string =>
                "{$address->display_full_address}"
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Tipo'))
                    ->badge(),
                Tables\Columns\TextColumn::make('display_short_address')
                    ->label(__('Endereço')),
                Tables\Columns\TextColumn::make('zipcode')
                    ->label(__('CEP')),
                Tables\Columns\TextColumn::make('city')
                    ->label(__('Cidade/Uf'))
                    ->formatStateUsing(
                        fn (Address $address): string =>
                        "{$address->city}-{$address->uf}"
                    ),
                Tables\Columns\IconColumn::make('is_main')
                    ->label(__('Principal'))
                    ->icon(
                        fn (bool $state): string =>
                        match ($state) {
                            false => 'heroicon-m-minus-small',
                            true => 'heroicon-o-check-circle',
                        }
                    )
                    ->color(
                        fn (bool $state): string =>
                        match ($state) {
                            true => 'success',
                            default => 'gray',
                        }
                    ),
            ])
            // ->reorderable('order')
            ->defaultSort(
                fn (Builder $query): Builder =>
                $query->orderBy('is_main', 'desc')
                    ->orderBy('created_at', 'desc')
            )
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make()
                            ->before($this->ensureUniqueMainAddressCallback()),
                    ])
                        ->dropdown(false),
                    Tables\Actions\DeleteAction::make()
                        ->before($this->preventMainAddressDeleteCallback()),
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
                    ->before($this->ensureUniqueMainAddressCallback()),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name')
                    ->label(__('Tipo de endereço')),
                Infolists\Components\TextEntry::make('zipcode')
                    ->label(__('CEP')),
                Infolists\Components\TextEntry::make('city')
                    ->label(__('Cidade/Uf'))
                    ->formatStateUsing(
                        fn (Address $address): string =>
                        "{$address->city}-{$address->uf}"
                    ),
                Infolists\Components\TextEntry::make('district')
                    ->label(__('Bairro')),
                Infolists\Components\TextEntry::make('address_line')
                    ->label(__('Endereço')),
                Infolists\Components\TextEntry::make('number')
                    ->label(__('Nº')),
                Infolists\Components\TextEntry::make('complement')
                    ->label(__('Complemento')),
                Infolists\Components\TextEntry::make('reference')
                    ->label(__('Ponto de referência'))
                    ->columnSpan(2),
                Infolists\Components\TextEntry::make('created_at')
                    ->label(__('Cadastro'))
                    ->dateTime('d/m/Y H:i'),
                Infolists\Components\TextEntry::make('updated_at')
                    ->label(__('Últ. atualização'))
                    ->dateTime('d/m/Y H:i'),
            ])
            ->columns(3);
    }

    private function ensureUniqueMainAddressCallback(): Closure
    {
        return function (AddressService $service, array $data, Address $address, RelationManager $livewire): void {
            $service->ensureUniqueMainAddress($data, $address, $livewire);
        };
    }

    private function preventMainAddressDeleteCallback(): Closure
    {
        return function (AddressService $service, Tables\Actions\DeleteAction $action, Address $address, RelationManager $livewire): void {
            $service->preventMainAddressDeleteWhenMultiple($action, $address, $livewire);
        };
    }
}
