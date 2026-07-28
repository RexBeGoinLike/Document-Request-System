<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Schemas;

use App\DocumentRequestStatus;
use App\Models\DocumentType;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Flex;
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
                Flex::make([
                        ToggleButtons::make('status')
                            ->inline()
                            ->label('Status')
                            ->live()
                            ->extraAttributes([
                                'class' => '[&_button:disabled]:opacity-100 [&_button:disabled]:bg-gray-100 [&_button:disabled]:text-gray-400 [&_button:disabled]:border-gray-300 [&_button:disabled]:cursor-not-allowed dark:[&_button:disabled]:bg-gray-800',
                            ])
                            ->options(fn (Get $get) => collect(DocumentRequestStatus::cases())
                                ->when($get('is_mailing'), fn ($collection) => $collection
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
                            ->extraAttributes([
                                'class' => '[&_.fi-btn]:[--btn-bg:var(--color-600)] [&_.fi-btn]:[--btn-text:white]'
                            ])
                            ->grouped()
                            ->required()
                            ->grow(false),

                        Group::make()
                            ->relationship('deliveryDetail', condition: fn (Get $get) => $get('is_mailing'))
                            ->schema([
                                TextInput::make('courier_name')
                                    ->label('Courier Name')
                                    ->columnSpan(4)
                                    ->maxLength(20)
                                    ->visible(fn (Get $get, $record): bool =>
                                        $get('../status') === DocumentRequestStatus::DISPATCHED->value

                                        ||  $get('../status') === DocumentRequestStatus::DELIVERED->value
                                        && $get('../is_mailing'))
                                    ->required(fn (Get $get, $record): bool =>
                                        $get('../status') === DocumentRequestStatus::DISPATCHED->value

                                        ||  $get('../status') === DocumentRequestStatus::DELIVERED->value
                                        && $get('../is_mailing')),

                                TextInput::make('tracking_number')
                                    ->label('Tracking Number')
                                    ->columnSpan(4)
                                    ->maxLength(20)
                                    ->visible(fn (Get $get, $record): bool =>
                                        $get('../status') === DocumentRequestStatus::DISPATCHED->value
                                        ||  $get('../status') === DocumentRequestStatus::DELIVERED->value
                                        && $get('../is_mailing'))
                                    ->required(fn (Get $get, $record): bool =>
                                        $get('../status') === DocumentRequestStatus::DISPATCHED->value

                                        ||  $get('../status') === DocumentRequestStatus::DELIVERED->value
                                        && $get('../is_mailing')),
                                ])
                            ->columns(8),

                        Group::make()
                            ->relationship('representative', condition: fn (Get $get) => $get('has_representative'))
                            ->schema([
                                FileUpload::make('authorization_letter')
                                    ->label('Authorization Letter (PDF)')
                                    ->disk('attachments')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->visibility('private')
                                    ->columnSpan(4),

                            ])
                                ->visible(fn (Get $get) => $get('has_representative')),
                    ])
                    ->columnSpanFull()
                    ->from('sm'),

                Section::make()
                    ->compact()
                    ->schema([
                        Group::make()
                            ->schema([
                                    Section::make('Requester Information')
                                        ->compact()
                                        ->schema([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('first_name')
                                                    ->label('First Name')
                                                    ->maxLength(100)
                                                    ->required()
                                                    ->columnSpan(3),

                                                TextInput::make('last_name')
                                                    ->label('Last Name')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->columnSpan(3),

                                                TextInput::make('middle_name')
                                                    ->label('Middle Name')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->columnSpan(3),

                                                TextInput::make('suffix')
                                                    ->label('Suffix')
                                                    ->maxLength(10)
                                                    ->columnSpan(3),

                                                TextInput::make('serial_number')
                                                    ->label('Serial Number')
                                                    ->required()
                                                    ->maxLength(6)
                                                    ->columnSpan(6),

                                                TextInput::make('class')
                                                    ->label('Class')
                                                    ->required()
                                                    ->length(4)
                                                    ->columnSpan(6),

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
                                            ->columnSpan(12)
                                    ])
                                        ->collapsible()
                                        ->columnSpanFull(),

                                    Repeater::make('requestQuantity')
                                        ->hiddenLabel()
                                        ->required()
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
                                                    DocumentType::query()->pluck('document_type', 'id')->all()
                                                )
                                                ->formatStateUsing(
                                                    fn (Get $get) => $get('document_type_id')
                                                )
                                                ->afterStateUpdated(
                                                    function ($state, Set $set, Get $get) {
                                                        $set('document_type_id', $state);
                                                        $set('fee', DocumentType::find($state)->first()->document_fee);
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
                                        ->addActionLabel('Add New Document'),

                            ])
                            ->columns(12)
                            ->columnSpan(7),

                        Group::make()
                            ->schema([
                                Section::make('Representative Information')
                                    ->compact()
                                    ->collapsible()
                                    ->schema([
                                        Checkbox::make('has_representative')
                                            ->label('I WILL HAVE SOMEONE COLLECT THE DOCUMENT/S FOR ME')
                                            ->columnSpanFull()
                                            ->live()
                                            ->afterStateUpdated(
                                                function ($state, Set $set) {
                                                    $set('representative.last_name', null);
                                                    $set('representative.first_name', null);
                                                    $set('representative.middle_name', null);
                                                    $set('representative.suffix', null);
                                                    $set('representative.office', null);
                                                    $set('representative.contact', null);
                                                }
                                            ),

                                        Group::make()
                                            ->relationship('representative', condition: fn (Get $get) => $get('has_representative'))
                                            ->schema([
                                                TextInput::make('first_name')
                                                    ->label('First Name')
                                                    ->columnSpan(6)
                                                    ->maxLength(100)
                                                    ->visible(fn (Get $get): bool => $get('../has_representative'))
                                                    ->required(fn (Get $get): bool => $get('../has_representative'))
                                                    ->dehydrated(true),

                                                TextInput::make('last_name')
                                                    ->label('Last Name')
                                                    ->columnSpan(6)
                                                    ->maxLength(100)
                                                    ->visible(fn (Get $get): bool => $get('../has_representative'))
                                                    ->required(fn (Get $get): bool => $get('../has_representative'))
                                                    ->dehydrated(),

                                                TextInput::make('middle_name')
                                                    ->label('Middle Name')
                                                    ->columnSpan(6)
                                                    ->maxLength(100)
                                                    ->visible(fn (Get $get): bool => $get('../has_representative'))
                                                    ->dehydrated(),

                                                TextInput::make('suffix')
                                                    ->label('Suffix')
                                                    ->columnSpan(6)
                                                    ->maxLength(10)
                                                    ->visible(fn (Get $get): bool => $get('../has_representative'))
                                                    ->dehydrated(),

                                                TextInput::make('relationship')
                                                    ->label('Relationship')
                                                    ->columnSpan(4)
                                                    ->maxLength(20)
                                                    ->visible(fn (Get $get)
                                                    : bool => $get('../has_representative'))
                                                    ->required(fn (Get $get): bool => $get('../has_representative'))
                                                    ->dehydrated(),

                                                TextInput::make('office')
                                                    ->label('Office')
                                                    ->maxLength(20)
                                                    ->columnSpan(4)
                                                    ->hidden(fn ($state): bool => $state ?? false)
                                                    ->visible(fn (Get $get): bool => $get('../has_representative'))
                                                    ->dehydrated(),

                                                TextInput::make('contact')
                                                    ->length(11)
                                                    ->label('Contact Number')
                                                    ->columnSpan(4)
                                                    ->visible(fn (Get $get): bool => $get('../has_representative'))
                                                    ->required(fn (Get $get): bool => $get('../has_representative'))
                                                    ->dehydrated(),
                                            ])
                                            ->columns(12)
                                            ->columnSpanFull()
                                    ])
                                    ->columnSpanFull()
                                    ->columns(12),

                                Section::make('Mailing Information')
                                    ->compact()
                                    ->collapsible()
                                    ->schema([
                                        Checkbox::make('is_mailing')
                                            ->label('I WANT THE DOCUMENT/S TO BE MAILED TO ME')
                                            ->columnSpanFull()
                                            ->live()
                                            ->visible(),

                                        Group::make()
                                            ->relationship('deliveryDetail', condition: fn (Get $get) => $get('is_mailing'))
                                            ->schema([


                                                TextInput::make('first_name')
                                                    ->label('First Name')
                                                    ->columnSpan(6)
                                                    ->maxLength(100)
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing'))
                                                    ->required(fn (Get $get): bool => $get('../is_mailing')),

                                                TextInput::make('last_name')
                                                    ->label('Last Name')
                                                    ->columnSpan(6)
                                                    ->maxLength(100)
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing'))
                                                    ->required(fn (Get $get): bool => $get('../is_mailing')),

                                                TextInput::make('middle_name')
                                                    ->label('Middle Name')
                                                    ->columnSpan(6)
                                                    ->maxLength(100)
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing')),

                                                TextInput::make('suffix')
                                                    ->label('Suffix')
                                                    ->columnSpan(6)
                                                    ->maxLength(10)
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing')),

                                                TextInput::make('contact_number')
                                                    ->label('Contact Number')
                                                    ->columnSpanFull()
                                                    ->required()
                                                    ->length(11)
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing')),

                                                TextInput::make('street_address')
                                                    ->required()
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing'))
                                                    ->columnSpanFull(),

                                                TextInput::make('region')
                                                    ->label('Province / Major HUC')
                                                    ->datalist(Province::pluck('name')->toArray())
                                                    ->live(debounce: 500)
                                                    ->afterStateUpdated(function (Set $set) {
                                                        $set('city', null);
                                                        $set('barangay', null);
                                                    })
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing'))
                                                    ->required(fn (Get $get): bool => $get('../is_mailing'))
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
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing'))
                                                    ->required(fn (Get $get): bool => $get('../is_mailing'))
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
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing'))
                                                    ->required(fn (Get $get): bool => $get('../is_mailing'))
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
                                                    ->live(onBlur: true)
                                                    ->columnSpan([4])
                                                    ->visible(fn (Get $get): bool => $get('../is_mailing'))
                                                    ->required(fn (Get $get): bool => $get('../is_mailing')),

                                            ])
                                            ->columns(12)
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull()
                                    ->columnSpanFull()
                                    ->columns(12),
                            ])
                            ->columns(12)
                            ->columnSpan(5)
                    ])
                    ->columnSpanFull()
                    ->columns(12),

            ]);
    }
}
