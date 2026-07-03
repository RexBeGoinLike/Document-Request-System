<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Schemas;

use App\DocumentRequestStatus;
use App\Filament\Guest\Resources\Tickets\Pages\CreateTicket;
use App\Models\DocumentType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;

class DocumentRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        ToggleButtons::make('status')
                            ->inline()
                            ->label('Status')
                            ->columnSpanFull()
                            ->live()
                            ->options(fn (Get $get) => collect(DocumentRequestStatus::cases())
                                ->when($get('is_self_collection'), fn ($collection) => $collection
                                    ->reject(fn ($case) => in_array($case, [
                                        DocumentRequestStatus::FOR_COLLECTION,
                                        DocumentRequestStatus::COLLECTED,
                                    ])),
                                    fn ($collection) => $collection->reject(fn ($case) => in_array($case, [
                                        DocumentRequestStatus::FOR_DISPATCH,
                                        DocumentRequestStatus::DISPATCHED,
                                        DocumentRequestStatus::DELIVERED,
                                    ]))
                                )
                                ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
                            )
                            ->icons(fn () => collect(DocumentRequestStatus::cases())
                                ->mapWithKeys(fn ($case) => [$case->value => $case->getIcon()])
                            )
                            ->colors(fn () => collect(DocumentRequestStatus::cases())
                                ->mapWithKeys(fn ($case) => [$case->value => $case->getColor()])
                            )
                            ->required(),

                        Group::make()
                            ->relationship('deliveryDetail')
                            ->schema([
                                TextInput::make('courier_name')
                                    ->label('Courier Name')
                                    ->columnSpan(4)
                                    ->visible(fn (Get $get, $record): bool =>
                                        $record?->status === DocumentRequestStatus::FOR_DISPATCH
                                        ||  $record?->status === DocumentRequestStatus::DISPATCHED
                                        ||  $record?->status === DocumentRequestStatus::DELIVERED
                                        && $record?->deliveryDetail() !== null),

                                TextInput::make('tracking_number')
                                    ->label('Tracking Number')
                                    ->columnSpan(4)
                                    ->visible(fn (Get $get, $record): bool =>
                                        $record?->status === DocumentRequestStatus::FOR_DISPATCH
                                        ||  $record?->status === DocumentRequestStatus::DISPATCHED
                                        ||  $record?->status === DocumentRequestStatus::DELIVERED
                                        && $get('../mailing')),
                                ])->columnSpan(8)->columns(8)

                    ])->columnSpanFull()->columns(12),

                Section::make('Fee Breakdown')
                    ->schema([
                        Repeater::make('requestQuantity')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Document Type')
                                    ->width(70),

                                TableColumn::make('Qty')
                                    ->width(5),

                                TableColumn::make('Fee')
                                    ->width(5),

                                TableColumn::make('Total Fee')
                                    ->width(20),
                            ])
                            ->relationship()
                            ->schema([

                                Select::make('document_type')
                                    ->label('Document Type')
                                    ->options(
                                        DocumentType::query()->pluck('document_type', 'document_type_id')->all()
                                    )
                                    ->formatStateUsing(
                                        fn (Get $get) => $get('document_type_id')
                                    )
                                    ->afterStateUpdated(
                                        function ($state, Set $set, Get $get) {
                                            $set('fee', DocumentType::where('document_type_id', $state)->first()->document_fee);
                                            $set('total_fee', $get('quantity') * $get('fee'));
                                        }
                                    )
                                    ->live()
                                    ->columnSpanFull(),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->afterStateUpdated(
                                        function ($state, Set $set, Get $get) {
                                            $set('total_fee', $state * $get('fee'));
                                        }
                                    )
                                    ->live()
                                    ->required(),

                                TextInput::make('fee')
                                    ->disabled()
                                    ->dehydrated()
                                    ->numeric()
                                    ->required(),

                                TextInput::make('total_fee')
                                    ->disabled()
                                    ->dehydrated()
                                    ->label('Total Fee')
                                    ->numeric()
                                    ->required(),

                                Hidden::make('document_type_id'),
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Add New Document')
                    ])->columnSpanFull(),

                Group::make()
                    ->schema([
                        Section::make('Requester Information')
                            ->schema([
                                TextInput::make('requester_first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->columnSpan(4),

                                TextInput::make('requester_last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->columnSpan(4),

                                TextInput::make('requester_middle_name')
                                    ->label('Middle Name')
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('requester_suffix')
                                    ->label('Suffix')
                                    ->columnSpan(2),

                                TextInput::make('cadet_serial_number')
                                    ->label('Serial Number')
                                    ->required()
                                    ->columnSpan(6),

                                TextInput::make('original_class')
                                    ->label('Original Class')
                                    ->required()
                                    ->columnSpan(6),

                                TextInput::make('street_address')
                                    ->required()
                                    ->columnSpanFull(),

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
                                    ->wrapOptionLabels(false)
                                    ->columnSpan([4])
                                    ->required(),

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
                                    ->wrapOptionLabels(false)
                                    ->columnSpan([4])
                                    ->required(),

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
                                    ->wrapOptionLabels(false)
                                    ->columnSpan([4])
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
                                    ->afterStateUpdated(function (CreateTicket $livewire, TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->columnSpan([4]),
                            ])
                            ->columns(12)
                            ->columnSpan(12),

                        Section::make('Representative Information')
                            ->schema([
                                Checkbox::make('has_representative')
                                    ->label('I WILL HAVE SOMEONE COLLECT THE DOCUMENT/S FOR ME')
                                    ->formatStateUsing(fn (Get $get) => !empty($get('representative_last_name')))
                                    ->columnSpanFull()
                                    ->live()
                                    ->afterStateUpdated(
                                        function ($state, Set $set) {
                                                $set('representative_last_name', null);
                                                $set('representative_first_name', null);
                                                $set('representative_middle_name', null);
                                                $set('representative_suffix', null);
                                                $set('representative_office', null);
                                                $set('representative_contact', null);
                                        }
                                    ),

                                TextInput::make('representative_first_name')
                                    ->label('First Name')
                                    ->columnSpan(4)
                                    ->visible(fn (Get $get): bool => $get('has_representative'))
                                    ->required(fn (Get $get): bool => $get('has_representative'))
                                    ->dehydrated(true),

                                TextInput::make('representative_last_name')
                                    ->label('Last Name')
                                    ->columnSpan(4)
                                    ->visible(fn (Get $get): bool => $get('has_representative'))
                                    ->required(fn (Get $get): bool => $get('has_representative'))
                                    ->dehydrated(),

                                TextInput::make('representative_middle_name')
                                    ->label('Middle Name')
                                    ->columnSpan(2)
                                    ->visible(fn (Get $get): bool => $get('has_representative'))
                                    ->required(fn (Get $get): bool => $get('has_representative'))
                                    ->dehydrated(),

                                TextInput::make('representative_suffix')
                                    ->label('Suffix')
                                    ->columnSpan(2)
                                    ->visible(fn (Get $get): bool => $get('has_representative'))
                                    ->dehydrated(),

                                TextInput::make('representative_office')
                                    ->label('Office')
                                    ->columnSpan(6)
                                    ->visible(fn (Get $get): bool => $get('has_representative'))
                                    ->required(fn (Get $get): bool => $get('has_representative'))
                                    ->dehydrated(),

                                TextInput::make('representative_contact')
                                    ->label('Contact Number')
                                    ->columnSpan(6)
                                    ->visible(fn (Get $get): bool => $get('has_representative'))
                                    ->required(fn (Get $get): bool => $get('has_representative'))
                                    ->dehydrated(),
                            ])
                            ->columns(12)
                            ->columnSpanFull(),

                        Section::make('Mailing Information')
                            ->relationship('deliveryDetail', condition: fn (?array $state): bool => (bool) $state['is_self_collection'])
                            ->schema([
                                    Checkbox::make('is_self_collection')
                                    ->label('I WANT THE DOCUMENT/S TO BE MAILED TO ME')
                                    ->columnSpanFull()
                                    ->live()
                                    ->formatStateUsing(fn (Get $get) => !empty($get('recipient_last_name')))

                                    ->visible(),

                                TextInput::make('recipient_first_name')
                                    ->label('First Name')
                                    ->columnSpan(4)
                                    ->visible(fn (Get $get): bool => $get('is_self_collection'))
                                    ->required(fn (Get $get): bool => $get('is_self_collection')),

                                TextInput::make('recipient_last_name')
                                    ->label('Last Name')
                                    ->columnSpan(4)
                                    ->visible(fn (Get $get): bool => $get('is_self_collection'))
                                    ->required(fn (Get $get): bool => $get('is_self_collection')),

                                TextInput::make('recipient_middle_name')
                                    ->label('Middle Name')
                                    ->columnSpan(2)
                                    ->visible(fn (Get $get): bool => $get('is_self_collection'))
                                    ->required(fn (Get $get): bool => $get('is_self_collection')),

                                TextInput::make('recipient_suffix')
                                    ->label('Suffix')
                                    ->columnSpan(2)
                                    ->visible(fn (Get $get): bool => $get('is_self_collection')),

                                TextInput::make('recipient_contact_number')
                                    ->label('Contact Number')
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get): bool => $get('is_self_collection')),

                                TextInput::make('street_address')
                                    ->required()
                                    ->visible(fn (Get $get): bool => $get('is_self_collection'))
                                    ->columnSpanFull(),

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
                                    ->wrapOptionLabels(false)
                                    ->columnSpan([4])
                                    ->required()
                                    ->visible(fn (Get $get): bool => $get('is_self_collection')),

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
                                    ->wrapOptionLabels(false)
                                    ->columnSpan([4])
                                    ->required()
                                    ->visible(fn (Get $get): bool => $get('is_self_collection')),

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
                                    ->wrapOptionLabels(false)
                                    ->columnSpan([4])
                                    ->required()
                                    ->visible(fn (Get $get): bool => $get('is_self_collection')),

                                TextInput::make('zip_code')
                                    ->label('Zip Code')
                                    ->numeric()
                                    ->validationAttribute('Zip Code')
                                    ->length(4)
                                    ->minValue(0000)
                                    ->maxValue(9999)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (CreateTicket $livewire, TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    })
                                    ->columnSpan([4])
                                    ->visible(fn (Get $get): bool => $get('is_self_collection')),

                            ])
                            ->columns(12)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->columns(12),

            ]);
    }
}
