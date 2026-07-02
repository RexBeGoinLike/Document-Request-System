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
        return Step::make('representative_step')
            ->label('Representative Information')
            ->schema([

                Section::make('representative_information')
                    ->heading('Representative Information')
                    ->schema([
                        Checkbox::make('has_representative')
                            ->dehydrated(false)
                            ->live()
                            ->label('I WILL HAVE SOMEONE COLLECT THE DOCUMENT/S FOR ME')
                            ->columnSpan(12),

                        Group::make()
                            ->schema([
                                TextInput::make('representative_first_name')
                                    ->label('First Name')
                                    ->autocapitalize('words')
                                    ->columnSpan([4])
                                    ->required(fn (Get $get) : bool => $get('has_representative'))
                                    ->disabled(fn (Get $get) : bool => !  $get('has_representative')),

                                TextInput::make('representative_last_name')
                                    ->label('Last Name')
                                    ->autocapitalize('words')
                                    ->columnSpan([3])
                                    ->required(fn (Get $get) : bool => $get('has_representative'))
                                    ->disabled(fn (Get $get) : bool => ! $get('has_representative')),

                                TextInput::make('representative_middle_name')
                                    ->label('Middle Name')
                                    ->autocapitalize('words')
                                    ->columnSpan([3])
                                    ->required(fn (Get $get) : bool => $get('has_representative'))
                                    ->disabled(fn (Get $get) : bool => ! $get('has_representative')),

                                TextInput::make('representative_suffix')
                                    ->label('Suffix')
                                    ->autocapitalize('words')
                                    ->columnSpan([2])
                                    ->disabled(fn (Get $get) : bool => ! $get('has_representative')),

                                TextInput::make('representative_office')
                                    ->label('Office')
                                    ->autocapitalize('words')
                                    ->columnSpan([7])
                                    ->required(fn (Get $get) : bool => $get('has_representative'))
                                    ->disabled(fn (Get $get) : bool => ! $get('has_representative')),


                                TextInput::make('representative_contact')
                                    ->label('Contact Number')
                                    ->autocapitalize('words')
                                    ->columnSpan([5])
                                    ->required(fn (Get $get) : bool => $get('has_representative'))
                                    ->disabled(fn (Get $get) : bool => ! $get('has_representative')),
                            ])
                                ->columns(12)
                                ->columnSpanFull()
                                ->visible(fn (Get $get) : bool => $get('has_representative'))
                    ])->columns(12)
            ]);
    }
}
