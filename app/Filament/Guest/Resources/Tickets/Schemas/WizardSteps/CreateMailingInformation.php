<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps;

use App\Filament\Guest\Resources\Tickets\Pages\CreateTicket;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Exceptions\Halt;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;

class CreateMailingInformation
{
    public static function make(): Step{

        $checkboxPath = '../is_mailing';
        return Step::make('mailing_information')
            ->label('Mailing Information')
            ->schema([
                Checkbox::make('is_mailing')
                    ->live()
                    ->label('I WANT THE DOCUMENT/S TO BE MAILED TO ME (PHP 200 Delivery Fee)')
                    ->columnSpanFull(),

                Group::make()
                    ->relationship('deliveryDetail', condition: fn (Get $get) => $get('is_mailing'))
                    ->schema(components: [
                        Checkbox::make('use_details')
                            ->dehydrated(false)
                            ->live()
                            ->label('Use Requester\'s Information')
                            ->disabled(fn (Get $get) : bool => ! $get($checkboxPath))
                            ->afterStateUpdated(function (bool $state, Get $get, Set $set)
                            {
                                if($state){
                                    $set('first_name', $get('../first_name'));
                                    $set('last_name', $get('../last_name'));
                                    $set('middle_name', $get('../middle_name'));
                                    $set('suffix', $get('../suffix'));
                                    $set('street_address', $get('../street_address'));
                                    $set('region', $get('../region'));
                                    $set('city', $get('../city'));
                                    $set('barangay', $get('../barangay'));
                                    $set('zip_code', $get('../zip_code'));
                                    $set('contact_number', $get('../contact_number'));
                                }else{
                                    $set('first_name', null);
                                    $set('last_name', null);
                                    $set('middle_name', null);
                                    $set('suffix', null);
                                    $set('street_address', null);
                                    $set('region', null);
                                    $set('city', null);
                                    $set('barangay', null);
                                    $set('zip_code', null);
                                    $set('contact_number', null);
                                }
                            })
                            ->columnSpanFull(),

                        Section::make('Mailing Information')
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('First Name')
                                    ->validationAttribute('First Name')
                                    ->autocapitalize('words')
                                    ->required(fn (Get $get) : bool => $get($checkboxPath))
                                    ->columnSpan([4])
                                    ->maxLength(100),

                                TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->validationAttribute('Last Name')
                                    ->autocapitalize('words')
                                    ->required(fn (Get $get) : bool => $get($checkboxPath))
                                    ->columnSpan([3])
                                    ->maxLength(100),

                                TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->validationAttribute('Middle Name')
                                    ->autocapitalize('words')
                                    ->required(fn (Get $get) : bool => $get($checkboxPath))
                                    ->columnSpan([3])
                                    ->maxLength(100),

                                TextInput::make('suffix')
                                    ->label('Suffix')
                                    ->validationAttribute('Suffix')
                                    ->autocapitalize('words')
                                    ->columnSpan([2])
                                    ->maxLength(10),

                                TextInput::make('contact_number')
                                    ->validationAttribute('Contact Number')
                                    ->autocapitalize('characters')
                                    ->tel()
                                    ->length(11)
                                    ->required(fn (Get $get) : bool => $get($checkboxPath))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (CreateTicket $livewire, TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->columnSpan([12]),

                            ])->columns(12)
                            ->visible(fn (Get $get) : bool => $get($checkboxPath))
                            ->disabled(fn (Get $get) : bool => ! $get($checkboxPath)),

                        Section::make('Mailing Address Information')
                            ->schema([
                                TextInput::make('street_address')
                                    ->required(fn (Get $get) : bool => $get($checkboxPath))
                                    ->columnSpan([12]),

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
                                    ->required(fn (Get $get) : bool => $get($checkboxPath))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (CreateTicket $livewire, TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->columnSpan([4]),
                            ])->columns(12)
                            ->visible(fn (Get $get) : bool => $get($checkboxPath))
                            ->disabled(fn (Get $get) : bool => ! $get($checkboxPath)),
                    ])
            ]);

    }
}
