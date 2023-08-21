<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\ProfileInfos\Uf;
use App\Models\Address;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $modelLabel = 'endereço';

    // protected static ?string $pluralModelLabel = 'endereços';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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
                    // ->helperText(__('Logradouro'))
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
                    ->label(__('Utilizar como endereço principal')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn (Address $record): string => "{$record->display_full_address}")
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
                    ->formatStateUsing(fn (Address $record): string => "{$record->city}-{$record->uf}")
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_main')
                    ->label(__('Principal'))
                    ->icon(fn (bool $state): string => match ($state) {
                        false => 'heroicon-m-minus-small',
                        true => 'heroicon-o-check-circle',
                    })
                    ->color(fn (bool $state): string => match ($state) {
                        true => 'success',
                        default => 'gray',
                    }),
                // Tables\Columns\TextColumn::make('uf')
                //     ->label(__('Estado'))
                //     ->sortable(),                    
            ])
            // ->reorderable('order')
            ->filters([
                //
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
                    ->size(ActionSize::ExtraSmall)
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
                Infolists\Components\TextEntry::make('name')
                    ->label(__('Tipo de endereço')),
                Infolists\Components\TextEntry::make('zipcode')
                    ->label(__('CEP')),
                Infolists\Components\TextEntry::make('city')
                    ->label(__('Cidade/Uf'))
                    ->formatStateUsing(fn (Address $record): string => "{$record->city}-{$record->uf}"),
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
}
