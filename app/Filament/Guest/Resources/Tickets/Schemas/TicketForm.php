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

                    //STEP 2
                    Wizard\Step::make('Request for Document/s')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Grid::make(18)
                                ->schema([
                                    Repeater::make('Type of Document/s')
                                    ->table([
                                        TableColumn::make('Selected')->width('10%'),
                                        TableColumn::make('Type')->width('60%'),
                                        TableColumn::make('Fee')->width('30%'),
                                        TableColumn::make('Quantity')->width('20%'),
                                    ])
                                        ->default(function () {
                                            $documents = DocumentType::query()
                                                ->whereNot('document_classification', 'mailing')
                                                ->orderBy('document_classification',
                                                'desc')
                                                ->orderBy('document_type')
                                                ->get();

                                            $rows = [];
                                            $currentCategory = null;

                                            foreach ($documents as $document) {
                                                if ($currentCategory !== $document->document_classification) {
                                                    $currentCategory = $document->document_classification;

                                                    $rows[] = [
                                                        'is_header' => true,
                                                        'document_classifications' => strtoupper($currentCategory),
                                                        'document_type_id' => null,
                                                        'document_type' => '',
                                                        'fee' => 0,
                                                        'Selected' => false,
                                                        'quantity' => 0,
                                                    ];
                                                }

                                                $rows[] = [
                                                    'is_header' => false,
                                                    'document_type_id' => $document->id,
                                                    'document_type' => $document->document_type,
                                                    'fee' => $document->document_fee,
                                                    'Selected' => false,
                                                    'quantity' => 0,
                                                ];
                                            }

                                            return $rows;
                                        })

                                    ->schema([

                                        TextEntry::make('Category')
                                            ->hiddenLabel()
                                            ->label('Category')
                                            ->default(fn (Get $get) => $get('document_classifications'))
                                            ->columnSpanFull()
                                            ->visible(fn (Get $get) => (bool) $get('is_header')),

                                        Checkbox::make('Selected')
                                            ->live()
                                            ->columnSpan(2)
                                            ->afterStateUpdated(function (bool $state, Set $set) {
                                                if(!$state){
                                                    $set('quantity', 0);
                                                }else{
                                                    $set('quantity', 1);
                                                }
                                            })->visible(fn (Get $get) => (bool) ! $get('is_header')),

                                        TextInput::make('document_type')
                                            ->disabled()
                                            ->dehydrated()
                                            ->visible(fn (Get $get) => ! (bool) $get('is_header')),

                                        TextInput::make('fee')
                                            ->label('fee')
                                            ->disabled()
                                            ->dehydrated()->visible(fn (Get $get) => ! (bool) $get('is_header')),

                                        TextInput::make('quantity')
                                            ->label('Quantity')
                                            ->numeric()
                                            ->disabled(fn (Get $get) : bool => !$get('Selected'))
                                            ->required()->visible(fn (Get $get) => ! (bool) $get('is_header')),

                                        TextInput::make('document_type_id')->hidden(),

                                        TextEntry::make('Category')
                                            ->hiddenLabel()
                                            ->label('Category')
                                            ->default(fn (Get $get) => $get('document_classifications'))
                                            ->columnSpanFull()
                                            ->visible(fn (Get $get) => (bool) $get('is_header')),
                                    ])
                                    ->columns(10)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->columnSpan([11]),

                                    Section::make('Fee Breakdown')
                                    ->schema([


                                    ])->columnSpan([7]),


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
