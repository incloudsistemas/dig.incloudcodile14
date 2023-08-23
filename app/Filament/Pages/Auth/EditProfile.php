<?php

namespace App\Filament\Pages\Auth;

use App\Enums\ProfileInfos\EducationalLevel;
use App\Enums\ProfileInfos\Gender;
use App\Enums\ProfileInfos\MaritalStatus;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Support\RawJs;

class EditProfile extends BaseEditProfile
{
    protected static string $view = 'filament.pages.auth.edit-profile';

    protected static string $layout = 'filament-panels::components.layout.index';

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Infos. Gerais'))
                    ->description(__('Visão geral e informações fundamentais sobre o usuário.'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Nome'))
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->confirmed()
                            ->maxLength(255)
                            ->live(debounce: 500)
                            ->afterStateUpdated(
                                fn ($state, callable $set) =>
                                $set('email_confirmation', $state)
                            )
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('additional_emails')
                            ->label(__('Email(s) adicionais'))
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label(__('Email'))
                                    // ->required()
                                    ->live(debounce: 500)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Tipo de email'))
                                    ->helperText(__('Nome identificador. Ex: Pessoal, Trabalho...'))
                                    ->minLength(2)
                                    ->maxLength(255)
                                    ->datalist([
                                        'Pessoal',
                                        'Trabalho',
                                        'Outros'
                                    ])
                                    ->autocomplete(false),
                            ])
                            ->itemLabel(
                                fn (array $state): ?string =>
                                $state['email'] ?? null
                            )
                            ->addActionLabel(__('Adicionar email'))
                            ->defaultItems(0)
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->collapseAllAction(
                                fn (Action $action) =>
                                $action->label(__('Minimizar todos'))
                            )
                            // ->deleteAction(
                            //     fn (Action $action) => 
                            //     $action->requiresConfirmation()
                            // )
                            ->columnSpanFull()
                            ->columns(2),
                        Forms\Components\Repeater::make('phones')
                            ->label(__('Telefone(s) de contato'))
                            ->schema([
                                Forms\Components\TextInput::make('number')
                                    ->label(__('Nº do telefone'))
                                    ->mask(
                                        RawJs::make(<<<'JS'
                                            $input.length === 14 ? '(99) 9999-9999' : '(99) 99999-9999'
                                        JS)
                                    )
                                    // ->required()
                                    ->live(debounce: 500)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Tipo de contato'))
                                    ->helperText(__('Nome identificador. Ex: Celular, Whatsapp, Casa, Trabalho...'))
                                    ->minLength(2)
                                    ->maxLength(255)
                                    ->datalist([
                                        'Celular',
                                        'Whatsapp',
                                        'Casa',
                                        'Trabalho',
                                        'Outros'
                                    ])
                                    ->autocomplete(false),

                            ])
                            ->itemLabel(
                                fn (array $state): ?string =>
                                $state['number'] ?? null
                            )
                            ->addActionLabel(__('Adicionar telefone'))
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->collapseAllAction(
                                fn (Action $action) =>
                                $action->label(__('Minimizar todos'))
                            )
                            // ->deleteAction(
                            //     fn (Action $action) => 
                            //     $action->requiresConfirmation()
                            // )
                            ->columnSpanFull()
                            ->columns(2),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make(__('Acesso ao Sistema'))
                    ->description(__('Gerencie o nível de acesso do usuário.'))
                    ->schema([
                        Forms\Components\TextInput::make('email_confirmation')
                            ->label(__('Usuário'))
                            ->placeholder(__('Preencha o email'))
                            ->required()
                            ->readOnly()
                            ->columnSpanFull(),
                        $this->getPasswordFormComponent()
                            ->helperText(__('Preencha apenas se desejar alterar a senha. Min. de 8 dígitos.'))
                            ->minLength(8)
                            ->maxLength(255),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Forms\Components\Section::make(__('Infos. Complementares'))
                    ->description(__('Forneça informações adicionais relevantes.'))
                    ->schema([
                        Forms\Components\TextInput::make('cpf')
                            ->label(__('CPF'))
                            ->mask('999.999.999-99')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rg')
                            ->label(__('RG'))
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->label(__('Sexo'))
                            ->options(Gender::asSelectArray())
                            ->in(Gender::getValues())
                            ->native(false),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label(__('Dt. nascimento'))
                            ->format('d/m/Y')
                            ->maxDate(now()),
                        Forms\Components\Select::make('marital_status')
                            ->label(__('Estado civil'))
                            ->options(MaritalStatus::asSelectArray())
                            ->searchable()
                            ->in(MaritalStatus::getValues())
                            ->native(false),
                        Forms\Components\Select::make('educational_level')
                            ->label(__('Escolaridade'))
                            ->options(EducationalLevel::asSelectArray())
                            ->searchable()
                            ->in(EducationalLevel::getValues())
                            ->native(false),
                        Forms\Components\TextInput::make('nationality')
                            ->label(__('Nacionalidade'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('citizenship')
                            ->label(__('Naturalidade'))
                            ->maxLength(255),
                        Forms\Components\Textarea::make('complement')
                            ->label(__('Complemento'))
                            ->rows(4)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['email_confirmation'] = $data['email'];
        return $data;
    }
}
