<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Exceptions\Halt;
use Filament\Schemas\Components\Group;

class CreateRepresentativeInformation
{
    public static function make(): Step{

        $checkboxPath = '../has_representative';

        return Step::make('step')
            ->label('Representative Information')
            ->schema([
                Checkbox::make('has_representative')
                    ->live()
                    ->label('I WILL HAVE SOMEONE COLLECT THE DOCUMENT/S FOR ME')
                    ->columnSpanFull(),

                Section::make('information')
                    ->relationship('representative', condition: fn (Get $get) => (bool) $get('has_representative'))
                    ->heading('Representative Information')
                    ->schema([

                        TextInput::make('first_name')
                            ->label('First Name')
                            ->autocapitalize('words')
                            ->columnSpan([4])
                            ->maxLength(100)
                            ->required(fn (Get $get) : bool => $get($checkboxPath ))
                            ->disabled(fn (Get $get) : bool => !  $get($checkboxPath )),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->autocapitalize('words')
                            ->columnSpan([3])
                            ->maxLength(100)
                            ->required(fn (Get $get) : bool => $get($checkboxPath ))
                            ->disabled(fn (Get $get) : bool => ! $get($checkboxPath )),

                        TextInput::make('middle_name')
                            ->label('Middle Name')
                            ->autocapitalize('words')
                            ->columnSpan([3])
                            ->maxLength(100)
                            ->disabled(fn (Get $get) : bool => ! $get($checkboxPath )),

                        TextInput::make('suffix')
                            ->label('Suffix')
                            ->autocapitalize('words')
                            ->columnSpan([2])
                            ->maxLength(10)
                            ->disabled(fn (Get $get) : bool => ! $get($checkboxPath )),

                        TextInput::make('relationship')
                            ->label('Relationship')
                            ->autocapitalize('words')
                            ->columnSpan(4)
                            ->maxLength(20)
                            ->required(fn (Get $get) : bool => $get($checkboxPath ))
                            ->disabled(fn (Get $get) : bool => ! $get($checkboxPath )),

                        TextInput::make('office')
                            ->label('Office')
                            ->autocapitalize('words')
                            ->columnSpan([4])
                            ->maxLength(20)
                            ->disabled(fn (Get $get) : bool => ! $get($checkboxPath )),

                        TextInput::make('contact')
                            ->label('Contact Number')
                            ->autocapitalize('words')
                            ->columnSpan(4)
                            ->length(11)
                            ->required(fn (Get $get) : bool => $get($checkboxPath ))
                            ->disabled(fn (Get $get) : bool => ! $get($checkboxPath )),
                    ])
                    ->visible(fn (Get $get) : bool => $get('has_representative'))
                    ->columnSpanFull()
                    ->columns(12)
            ])
            ->columnSpanFull();
    }
}
