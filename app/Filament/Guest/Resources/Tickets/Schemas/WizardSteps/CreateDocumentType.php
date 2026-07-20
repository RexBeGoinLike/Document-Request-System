<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps;

use App\Models\DocumentType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Exceptions\Halt;

class CreateDocumentType
{
    public static function make() : Step {
        return Step::make('document_step')
            ->label('Document/s')
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
                                                'document_type_id' => $document->id,
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
                                                                'fee' => $get('fee'),
                                                                'quantity' => $get('quantity'),
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

                                            TextEntry::make('fee')
                                                ->alignRight()
                                                ->numeric()
                                                ->prefix('PHP '),

                                            TextInput::make('quantity')
                                                ->numeric()
                                                ->disabled(fn (Get $get) => ! $get('selected'))
                                                ->live()
                                                ->afterStateUpdated(function (?string $state, Get $get, Set $set) {

                                                    $documentTypeId = $get('document_type_id');


                                                    if ((int) $state <= 0 && $state != null) {
                                                        $breakdown = collect($get('../../fee_breakdown') ?? [])
                                                            ->filter(fn ($item) => filled($item['document_type_id'] ?? null)) // drop empty placeholder rows
                                                            ->values();

                                                        $set('selected', false);
                                                        $set('quantity', 0);

                                                        $breakdown = $breakdown->reject(
                                                            fn ($item) => $item['document_type_id'] === $documentTypeId
                                                        )->values();

                                                        $set('../../fee_breakdown', $breakdown->toArray());
                                                        return;
                                                    }

                                                    $breakdown = collect($get('../../fee_breakdown') ?? [])
                                                        ->filter(fn ($item) => filled($item['document_type_id'] ?? null))
                                                        ->values();


                                                    $breakdown = $breakdown->map(function ($item) use ($documentTypeId, $get, $state) {
                                                        if ($item['document_type_id'] === $documentTypeId) {
                                                            $item['quantity'] = $get('quantity');
                                                            $item['total_fee'] = $get('fee') * (float) $state;
                                                        }

                                                        return $item;
                                                    })->values();

                                                    $set('../../fee_breakdown', $breakdown->toArray());
                                                })
                                                ->minValue(0)
                                                ->maxValue(2)
                                                ->extraInputAttributes([
                                                    'onkeypress' => 'return event.charCode !== 45', // Blocks '-' key
                                                ]),

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
                            ->columnSpan(fn (Get $get) : int => filled($get('fee_breakdown')) ? 7 : 12)
                            ->compact()
                            ->hiddenLabel()
                            ->extraAttributes([
                                'style' => 'max-height: 350px; overflow-y: auto;'
                            ])
                            ->dehydrated(false),

                        Group::make()
                            ->schema([
                                Section::make('Summary of Requested Document\'s')
                                    ->schema([
                                        Repeater::make('fee_breakdown')
                                            ->relationship('requestQuantity')
                                            ->hiddenLabel()
                                            ->default([])
                                            ->compact()
                                            ->schema([
                                                TextEntry::make('type_quantity')
                                                    ->hiddenLabel()
                                                    ->getStateUsing(function (Get $get) {
                                                        return $get('document_type') . ' x ' . $get('quantity');
                                                    })
                                                    ->columnSpanFull(),

                                                Hidden::make('quantity'),
                                                Hidden::make('document_type')->dehydrated(false),
                                                Hidden::make('total_fee'),
                                                Hidden::make('quantity'),
                                                Hidden::make('document_type_id'),
                                                Hidden::make('fee'),

                                            ])
                                            ->addable(false)
                                            ->deletable(false)
                                            ->reorderable(false)
                                            ->columns(  2)


                                    ]),

                                TextEntry::make('total_fee_indicator')
                                    ->label('Total Fee')
                                    ->getStateUsing(function (Get $get) {
                                        $breakdown = collect($get('fee_breakdown') ?? [])
                                            ->filter(fn ($item) => filled($item['document_type_id'] ?? null))
                                            ->values();

                                        return $breakdown->sum('total_fee');
                                    })
                                    ->prefix('PHP ')
                                    ->disabled()
                            ])
                            ->columnSpan(5)
                            ->visible(fn (Get $get) => filled($get('fee_breakdown')))
                    ])
            ])
            ->afterValidation(function (Get $get) {
                $breakdown = collect($get('fee_breakdown') ?? [])
                    ->filter(fn ($item) => filled($item['document_type_id'] ?? null));

                if ($breakdown->isEmpty()) {
                    Notification::make()
                        ->title('Selection required')
                        ->body('Please select at least one document type before continuing.')
                        ->danger()
                        ->send();

                    throw new Halt();
                }
            });
    }
}
