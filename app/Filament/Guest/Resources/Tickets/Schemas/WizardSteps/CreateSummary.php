<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;

class CreateSummary
{
    public static function make(): Step{
        return Step::make('summary_step')
            ->label('Summary of Information')
            ->schema([
                Section::make('Fee Breakdown')
                    ->schema([
                        TextEntry::make('s_fee_breakdown_doc')
                            ->label('Document Fees')
                            ->columnSpanFull()
                            ->inlineLabel()
                            ->prefix('PHP '),

                        TextEntry::make('s_mailing_fee')
                            ->label('Mailing Fee')
                            ->prefix('PHP ')
                            ->inlineLabel()
                            ->columnSpanFull(),

                        Repeater::make('s_fee_breakdown')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Type')->width('40%'),
                                TableColumn::make('Fee')->width('20%'),
                                TableColumn::make('Quantity')->width('20%'),
                                TableColumn::make('Total Fee')->width('20%')
                            ])
                            ->schema([
                                TextEntry::make('document_type')
                                ->label('Document Type'),
                                TextEntry::make('fee'),
                                TextEntry::make('quantity'),
                                TextEntry::make('total_fee'),
                                Hidden::make('document_type_id'),
                            ])
                            ->columnSpanFull()
                            ->reorderable(false)
                            ->addable(false)
                            ->deletable(false),

                        TextEntry::make('s_total_fee')
                            ->label('Total Fee')
                            ->prefix('PHP ')
                            ->inlineLabel()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),



                Group::make()
                    ->schema([
                        Section::make('Requester\'s Information')
                            ->schema([
                                TextEntry::make('s_requester_name')
                                    ->label('Requester\'s Full Name')
                                    ->columnSpan(4),

                                TextEntry::make('s_cadet_serial_number')
                                    ->label('Serial Number')
                                    ->columnSpan(4),

                                TextEntry::make('s_original_class')
                                    ->label('Cadet Class')
                                    ->columnSpan(4),

                                TextEntry::make('s_contact_number')
                                    ->label('Contact Number')
                                    ->columnSpan(4),

                                TextEntry::make('s_requester_address')
                                    ->label('Requester\'s Full Address')
                                    ->columnSpan(8),
                            ])
                            ->columns(12)
                            ->columnSpanFull(),

                        Section::make('Representative Information')
                            ->schema([
                                Checkbox::make('s_rep_selected')
                                    ->label('I WILL HAVE SOMEONE COLLECT THE DOCUMENT/S FOR ME')
                                    ->live()
                                    ->dehydrated(false)
                                    ->disabled()
                                    ->columnSpanFull(),

                                TextEntry::make('s_representative_full_name')
                                    ->label('Representative\'s Full Name')
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get) => $get('s_rep_selected')),

                                TextEntry::make('s_representative_office')
                                    ->label('Office')
                                    ->columnSpan(7)
                                    ->visible(fn (Get $get) => $get('s_rep_selected')),

                                TextEntry::make('s_representative_contact')
                                    ->label('Contact Number')
                                    ->columnSpan([5])
                                    ->visible(fn (Get $get) => $get('s_rep_selected')),
                            ])
                            ->columns(12)
                            ->columnSpan(6),

                        Section::make('Mailing Information')
                            ->schema([
                                Checkbox::make('s_mailing_selected')
                                    ->label('I WANT THE DOCUMENT/S TO BE MAILED TO ME')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),

                                TextEntry::make('s_mailing_full_name')
                                    ->label('Full Name')
                                    ->visible(fn (Get $get) => $get('s_mailing_selected'))
                                    ->columnSpan(6),

                                TextEntry::make('s_mailing_contact')
                                    ->label('Contact Number')
                                    ->visible(fn (Get $get) => $get('s_mailing_selected'))
                                    ->columnSpan(6),

                                TextEntry::make('s_mailing_full_address')
                                    ->label('Mailing Address')
                                    ->visible(fn (Get $get) => $get('s_mailing_selected'))
                                    ->columnSpanFull(),

                            ])
                            ->columns(12)
                            ->columnSpan(6),

                    ])
                    ->columns(12)
                    ->columnSpanFull(),



            ])
            ->columns(12);
    }
}
