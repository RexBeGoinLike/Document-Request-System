<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps;

use App\Filament\Guest\Resources\Tickets\Pages\CreateTicket;
use Filament\Forms\Components\Checkbox;
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
        return Step::make('mailing_information')
            ->label('Mailing Information')
            ->schema([
                Group::make()
                    ->relationship('deliveryDetail', condition: fn (?array $state): bool => (bool) ($state['for_mailing'] ?? false))
                    ->schema([
                        Checkbox::make('for_mailing')
                            ->live()
                            ->dehydrated(false)
                            ->label('I WANT THE DOCUMENT/S TO BE MAILED TO ME')
                            ->columnSpanFull(),

                        Checkbox::make('use_details')
                            ->dehydrated(false)
                            ->live()
                            ->label('Use Requester\'s Information')
                            ->disabled(fn (Get $get) : bool => ! $get('for_mailing'))
                            ->afterStateUpdated(function (bool $state, Get $get, Set $set)
                            {
                                if($state){
                                    $set('recipient_first_name', $get('../requester_first_name'));
                                    $set('recipient_last_name', $get('../requester_last_name'));
                                    $set('recipient_middle_name', $get('../requester_middle_name'));
                                    $set('recipient_suffix', $get('../requester_suffix'));
                                    $set('street_address', $get('../street_address'));
                                    $set('region', $get('../region'));
                                    $set('city', $get('../city'));
                                    $set('barangay', $get('../barangay'));
                                    $set('zip_code', $get('../zip_code'));
                                    $set('recipient_contact_number', $get('../contact_number'));
                                }else{
                                    $set('recipient_first_name', null);
                                    $set('recipient_last_name', null);
                                    $set('recipient_middle_name', null);
                                    $set('recipient_suffix', null);
                                    $set('street_address', null);
                                    $set('region', null);
                                    $set('city', null);
                                    $set('barangay', null);
                                    $set('zip_code', null);
                                    $set('recipient_contact_number', null);
                                }
                            })
                            ->columnSpanFull(),

                        Section::make('Mailing Information')
                            ->schema([
                                TextInput::make('recipient_first_name')
                                    ->label('First Name')
                                    ->validationAttribute('First Name')
                                    ->autocapitalize('words')
                                    ->required(fn (Get $get) : bool => $get('for_mailing'))
                                    ->columnSpan([4]),

                                TextInput::make('recipient_last_name')
                                    ->label('Last Name')
                                    ->validationAttribute('Last Name')
                                    ->autocapitalize('words')
                                    ->required(fn (Get $get) : bool => $get('for_mailing'))
                                    ->columnSpan([3]),

                                TextInput::make('recipient_middle_name')
                                    ->label('Middle Name')
                                    ->validationAttribute('Middle Name')
                                    ->autocapitalize('words')
                                    ->required(fn (Get $get) : bool => $get('for_mailing'))
                                    ->columnSpan([3]),

                                TextInput::make('recipient_suffix')
                                    ->label('Suffix')
                                    ->validationAttribute('Suffix')
                                    ->autocapitalize('words')
                                    ->columnSpan([2]),

                                TextInput::make('recipient_contact_number')
                                    ->validationAttribute('Contact Number')
                                    ->autocapitalize('characters')
                                    ->tel()
                                    ->length(11)
                                    ->required(fn (Get $get) : bool => $get('for_mailing'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (CreateTicket $livewire, TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->columnSpan([12]),
                            ])->columns(12)
                            ->disabled(fn (Get $get) : bool => ! $get('for_mailing')),

                        Section::make('Mailing Address Information')
                            ->schema([
                                TextInput::make('street_address')
                                    ->required(fn (Get $get) : bool => $get('for_mailing'))
                                    ->columnSpan([12]),

                                Select::make('region')
                                    ->label('Region')
                                    ->options(Province::pluck('name', 'province_id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()

                                    ->afterStateUpdated(function (Set $set) {
                                        $set('city', null);
                                        $set('barangay', null);
                                    })
                                    ->columnSpan([4])
                                    ->required(fn (Get $get) : bool => $get('for_mailing')),

                                Select::make('city')
                                    ->label('City / Town')
                                    ->options(function (Get $get) {
                                        $provinceId = $get('region');
                                        if (! $provinceId) return [];

                                        return City::where('province_id', $provinceId)->pluck('name', 'city_id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->disabled(fn (Get $get): bool => ! $get('region'))
                                    ->afterStateUpdated(fn (Set $set) => $set('barangay', null))
                                    ->columnSpan([4])
                                    ->required(fn (Get $get) : bool => $get('for_mailing')),

                                Select::make('barangay')
                                    ->label('Barangay')
                                    ->options(function (Get $get) {
                                        $city_id = $get('city');
                                        if (! $city_id) return [];

                                        return Barangay::where('city_id', $city_id)->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->disabled(fn (Get $get): bool => ! $get('city'))
                                    ->columnSpan([4])
                                    ->required(fn (Get $get) : bool => $get('for_mailing')),

                                TextInput::make('zip_code')
                                    ->label('Zip Code')
                                    ->numeric()
                                    ->validationAttribute('Zip Code')
                                    ->length(4)
                                    ->minValue(0000)
                                    ->maxValue(9999)
                                    ->required(fn (Get $get) : bool => $get('for_mailing'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (CreateTicket $livewire, TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->columnSpan([4]),
                            ])->columns(12)
                            ->disabled(fn (Get $get) : bool => ! $get('for_mailing')),
                    ])
            ]);

    }
}
