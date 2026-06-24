<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas;

use App\Filament\Guest\Resources\Tickets\Pages\CreateTicket;
use App\Models\DocumentType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Tiptap\Nodes\Text;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;


class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    //STEP 3

                    Wizard\Step::make('Representative')
                        ->icon('heroicon-o-user')
                        ->schema([

                            Section::make('Representative Information')
                                ->schema([

                                    Checkbox::make('self_collect')
                                        ->dehydrated(false)
                                        ->live()
                                        ->label('I WILL PERSONALLY COLLECT THE DOCUMENT/S MYSELF')
                                        ->columnSpan(12),

                                    TextInput::make('representative_first_name')
                                        ->label('First Name')
                                        ->autocapitalize('words')
                                        ->columnSpan([4])
                                        ->required(fn (Get $get) : bool =>  $get('self_collect'))
                                        ->disabled(fn (Get $get) : bool => ! $get('self_collect')),

                                    TextInput::make('representative_last_name')
                                        ->label('Last Name')
                                        ->autocapitalize('words')
                                        ->columnSpan([3])
                                        ->required(fn (Get $get) : bool =>  $get('self_collect'))
                                        ->disabled(fn (Get $get) : bool => ! $get('self_collect')),

                                    TextInput::make('representative_middle_name')
                                        ->label('Middle Name')
                                        ->autocapitalize('words')
                                        ->columnSpan([3])
                                        ->required(fn (Get $get) : bool =>  $get('self_collect'))
                                        ->disabled(fn (Get $get) : bool => ! $get('self_collect')),

                                    TextInput::make('representative_suffix')
                                        ->label('Suffix')
                                        ->autocapitalize('words')
                                        ->columnSpan([2])
                                        ->disabled(fn (Get $get) : bool => ! $get('self_collect')),


                                    TextInput::make('representative_office')
                                        ->label('Office')
                                        ->autocapitalize('words')
                                        ->columnSpan([6])
                                        ->required(fn (Get $get) : bool =>  $get('self_collect'))
                                        ->disabled(fn (Get $get) : bool => ! $get('self_collect')),

                                    TextInput::make('representative_contact')
                                        ->label('Contact Number')
                                        ->autocapitalize('words')
                                        ->columnSpan([6])
                                        ->required(fn (Get $get) : bool =>  $get('self_collect'))
                                        ->disabled(fn (Get $get) : bool => ! $get('self_collect')),
                                ])->columns(12)
                        ]),

                    //STEP 2
                    Wizard\Step::make('Request for Document/s')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Grid::make(12)
                                ->schema([
                                    Section::make('Type of Document/s')
                                        ->schema(function () {
                                            $documentsGroup = DocumentType::query()
                                                ->whereNot('document_classification', 'mailing')
                                                ->orderBy('document_classification',
                                                'desc')
                                                ->orderBy('document_type')
                                                ->get()
                                                ->groupBy('document_classification');

                                            $sections = [];

                                            foreach ($documentsGroup as $classification => $documents) {
                                                   $sections[] = Repeater::make($classification)
                                                        ->table([
                                                            TableColumn::make('Selected')->width('10%')->hiddenHeaderLabel(),
                                                            TableColumn::make('Type')->width('60%'),
                                                            TableColumn::make('Fee')->width('30%'),
                                                            TableColumn::make('Quantity')->width('20%'),
                                                        ])
                                                        ->default(
                                                            $documents->map(fn ($document) => [
                                                                'document_type_id' => $document->document_type_id,
                                                                'document_type' => $document->document_type,
                                                                'fee' => $document->document_fee,
                                                                'selected' => false,
                                                                'quantity' => 0,
                                                            ])->toArray()
                                                        )
                                                        ->schema([
                                                            Checkbox::make('selected')
                                                                ->live()
                                                                ->afterStateUpdated(function (string $state, Get $get, Set $set) {
                                                                    $documentTypeId = $get('document_type_id');

                                                                    $breakdown = collect($get('../../fee_breakdown') ?? [])
                                                                        ->filter(fn ($item) => filled($item['document_type_id'] ?? null)) // drop empty placeholder rows
                                                                        ->values();

                                                                    if ($state) {
                                                                        $exists = $breakdown->contains('document_type_id', $documentTypeId);
                                                                        $set('quantity', 1);
                                                                        if (! $exists) {
                                                                            $breakdown->push([
                                                                                'document_type_id' => $documentTypeId,
                                                                                'document_type' => $get('document_type'),
                                                                                'total_fee' => $get('fee') * $get('quantity'),
                                                                            ]);
                                                                        }
                                                                    } else {
                                                                        $set('quantity', 0);
                                                                        $breakdown = $breakdown->reject(
                                                                            fn ($item) => $item['document_type_id'] === $documentTypeId
                                                                        )->values();
                                                                    }

                                                                    $set('../../fee_breakdown', $breakdown->toArray());
                                                                })
                                                            ->dehydrated(false),

                                                            TextEntry::make('document_type'),

                                                            TextEntry::make('fee'),

                                                            TextInput::make('quantity')
                                                                ->numeric()
                                                                ->disabled(fn (Get $get) => ! $get('selected'))
                                                                ->live()
                                                                ->afterStateUpdated(function (?string $state, Get $get, Set $set) {

                                                                    logger('QTY DEBUG', [
                                                                        'selected_raw' => $get('selected'),
                                                                        'document_type_id' => $get('document_type_id'),
                                                                        'state' => $state,
                                                                    ]);

                                                                    $documentTypeId = $get('document_type_id');

                                                                    $breakdown = collect($get('../../fee_breakdown') ?? [])
                                                                        ->filter(fn ($item) => filled($item['document_type_id'] ?? null))
                                                                        ->values();


                                                                    $breakdown = $breakdown->map(function ($item) use ($documentTypeId, $get, $state) {
                                                                        if ($item['document_type_id'] === $documentTypeId) {
                                                                            $item['total_fee'] = $get('fee') * (float) $state;
                                                                        }

                                                                        return $item;
                                                                    })->values();

                                                                    $set('../../fee_breakdown', $breakdown->toArray());
                                                                }),

                                                            Hidden::make('document_type_id')
                                                        ])
                                                        ->columns(4)
                                                        ->addable(false)
                                                        ->deletable(false)
                                                        ->reorderable(false);
                                            }

                                            return $sections;
                                        })
                                    ->columns(1)
                                    ->columnSpan([7]),

                                    Group::make()
                                        ->schema([
                                            Section::make('Fee Breakdown')
                                                ->schema([
                                        Repeater::make('fee_breakdown')
                                            ->relationship('requestQuantity')
                                            ->hiddenLabel()
                                            ->table([
                                                TableColumn::make('Type'),
                                                TableColumn::make('Total Fee'),
                                            ])
                                            ->compact()
                                            ->schema([
                                                TextInput::make('document_type')->disabled(),
                                                TextInput::make('total_fee')->disabled(),
                                                Hidden::make('document_type_id'),
                                                Hidden::make('fee'),
                                                Hidden::make('quantity'),
                                            ])
                                            ->addable(false)
                                            ->deletable(false)
                                            ->reorderable(false),

                                            TextEntry::make('total_fee')
                                                ->label('Total Fee')
                                                ->getStateUsing(function (Get $get) {
                                                    $breakdown = collect($get('fee_breakdown') ?? [])
                                                        ->filter(fn ($item) => filled($item['document_type_id'] ?? null))
                                                        ->values();

                                                    return $breakdown->sum('total_fee');
                                                })
                                                ->suffix(' PHP')
                                                ->disabled()

                                    ])
                                        ])
                                        ->columnSpan([5]),
                                ])
                        ]),

                    //STEP 1
                    Wizard\Step::make('Requestor Information')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Section::make('Personal Information')
                        ->schema([
                            Group::make()
                                ->schema([
                                    TextInput::make('requester_first_name')
                                        ->label('First Name')
                                        ->validationAttribute('First Name')
                                        ->autocapitalize('words')
                                        ->required()
                                        ->columnSpan([4]),

                                    TextInput::make('requester_last_name')
                                        ->label('Last Name')
                                        ->validationAttribute('Last Name')
                                        ->autocapitalize('words')
                                        ->required()
                                        ->columnSpan([3]),

                                    TextInput::make('requester_middle_name')
                                        ->label('Middle Name')
                                        ->validationAttribute('Middle Name')
                                        ->autocapitalize('words')
                                        ->required()
                                        ->columnSpan([3]),

                                    TextInput::make('requester_suffix')
                                        ->label('Suffix')
                                        ->validationAttribute('Suffix')
                                        ->autocapitalize('words')
                                        ->columnSpan([2]),
                                ])->columns(12),

                            Group::make()
                                ->schema([
                                    TextInput::make('cadet_serial_number')
                                        ->label('Cadet (CCAFP) Serial Number')
                                        ->validationAttribute('Serial Number')
                                        ->autocapitalize('words')
                                        ->required()
                                        ->columnSpan([4]),

                                    TextInput::make('original_class')
                                        ->validationAttribute('Original Class')
                                        ->autocapitalize('words')
                                        ->required()
                                        ->columnSpan([4]),

                                    TextInput::make('contact_number')
                                        ->validationAttribute('Contact Number')
                                        ->autocapitalize('characters')
                                        ->tel()
                                        ->length(11)
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (CreateTicket $livewire, TextInput $component) {
                                            $livewire->validateOnly($component->getStatePath());
                                        })
                                        ->columnSpan([4]),
                                ])->columns(12)
                        ]),
                        Section::make('Address Information')
                        ->schema([
                            TextInput::make('street_address')
                                ->required(),

                            Group ::make()
                            ->schema([
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
                                ->columnSpan([4])
                                ->required(),

                            ])->columns(12),

                            Group::make()
                            ->schema([
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
                            ])->columns(12),

                        ])->columnSpanFull(),
                    ]),

                    //END OF WIZARD
                ])->columnSpanFull()
            ]);
    }
}
