<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps;

use App\Filament\Guest\Resources\Tickets\Pages\CreateTicket;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Exceptions\Halt;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;

class CreateRequesterInformation
{
    public static function make() : Step
    {
        return Step::make('requester_step')
            ->label('Personal Information')
            ->schema([
                Section::make('requester_information')
                    ->heading('Requester Information')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->validationAttribute('First Name')
                            ->autocapitalize('words')
                            ->required()
                            ->maxLength(20)
                            ->columnSpan([4]),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->validationAttribute('Last Name')
                            ->autocapitalize('words')
                            ->required()
                            ->maxLength(15)
                            ->columnSpan([3]),

                        TextInput::make('middle_name')
                            ->label('Middle Name')
                            ->validationAttribute('Middle Name')
                            ->autocapitalize('words')
                            ->required()
                            ->maxLength(15)
                            ->columnSpan([3]),

                        TextInput::make('suffix')
                            ->label('Suffix')
                            ->validationAttribute('Suffix')
                            ->autocapitalize('words')
                            ->maxLength(20)
                            ->columnSpan([2]),

                        TextInput::make('serial_number')
                            ->label('Cadet (CCAFP) Serial Number')
                            ->validationAttribute('Serial Number')
                            ->autocapitalize('words')
                            ->required()
                            ->maxLength(4)
                            ->columnSpan([6]),

                        TextInput::make('class')
                            ->label('Class (Year)')
                            ->autocapitalize('words')
                            ->required()
                            ->numeric()
                            ->maxLength(4)
                            ->minLength(4)
                            ->columnSpan([6]),

                        TextInput::make('contact_number')
                            ->validationAttribute('Contact Number')
                            ->autocapitalize('characters')
                            ->tel()
                            ->length(11)
                            ->required()
                            ->live(onBlur: true)
                            ->columnSpan([4]),
                    ])
                    ->columns(12)
                    ->columnSpan(6),

                Section::make('requester_address_information')
                    ->heading('Requester Address')
                    ->schema([
                        TextInput::make('street_address')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('region')
                            ->label('Province / Major HUC')
                            ->datalist(Province::pluck('name')->toArray())
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Set $set) {
                                $set('city', null);
                                $set('barangay', null);
                            })
                            ->columnSpan(6)
                            ->required(),

                        TextInput::make('city')
                            ->label('City / Town')
                            ->datalist(function (Get $get) {
                                $region = $get('region');
                                if (! $region) return [];

                                $provinceId = Province::where('name', $region)->value('province_id');
                                if (! $provinceId) return [];

                                return City::where('province_id', $provinceId)->pluck('name')->toArray();
                            })
                            ->live(debounce: 500)
                            ->disabled(fn (Get $get): bool => ! $get('region'))
                            ->afterStateUpdated(fn (Set $set) => $set('barangay', null))
                            ->columnSpan(6)
                            ->required(),

                        TextInput::make('barangay')
                            ->label('Barangay')
                            ->datalist(function (Get $get) {
                                $city = $get('city');
                                if (! $city) return [];

                                $cityId = City::where('name', $city)->value('city_id');
                                if (! $cityId) return [];

                                return Barangay::where('city_id', $cityId)->pluck('name')->toArray();
                            })
                            ->live(debounce: 500)
                            ->disabled(fn (Get $get): bool => ! $get('city'))
                            ->columnSpan(6)
                            ->required(),

                        TextInput::make('zip_code')
                            ->label('Zip Code')
                            ->numeric()
                            ->validationAttribute('Zip Code')
                            ->length(4)
                            ->minValue(0000)
                            ->maxValue(9999)
                            ->required()
                            ->live(onBlur: true)
                            ->columnSpan([4]),
                    ])
                    ->columns(12)
                    ->columnSpan(6),
            ])
            ->columns(12);
    }
}
