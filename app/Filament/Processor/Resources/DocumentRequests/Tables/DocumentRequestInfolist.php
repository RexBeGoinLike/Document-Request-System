<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Tables;


use App\Models\DocumentType;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;
use Filament\Schemas\Components\Tabs\Tab;
class DocumentRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->size('lg')
                            ->columnSpanFull(),

                        TextEntry::make('user_processor')
                            ->label('Processor: ')
                            ->getStateUsing(fn ($record): string => $record?->processor->name ?? 'Not Assigned')
                            ->badge()
                            ->color(fn ($state): string => $state === 'Not Assigned' ? 'danger' : 'success')
                            ->inlineLabel()
                            ->columnSpan(5),

                        Tabs::make('General Information')
                            ->tabs([
                            Tab::make('Fee Breakdown')
                                ->schema([
                                    Group::make()
                                        ->schema([
                                            RepeatableEntry::make('requestQuantity')
                                                ->hiddenLabel()
                                                ->table([
                                                    TableColumn::make('Document Type')
                                                        ->width(60),

                                                    TableColumn::make('Fee')
                                                        ->width(5),

                                                    TableColumn::make('Qty')
                                                        ->width(5),

                                                    TableColumn::make('Total Fee')
                                                        ->width(30),
                                                ])
                                                ->schema([
                                                    TextEntry::make('documentType')
                                                        ->label('Document Type')
                                                        ->getStateUsing(function ($record) {
                                                            return
                                                                DocumentType::where('document_type_id', $record->document_type_id)->first()->document_type;
                                                        })
                                                        ->columnSpanFull(),
                                                    TextEntry::make('fee')
                                                        ->prefix('PHP ')
                                                        ->alignRight(),

                                                    TextEntry::make('quantity')
                                                        ->alignRight(),

                                                    TextEntry::make('total_fee')
                                                        ->prefix('PHP ')
                                                        ->alignRight(),
                                                ])
                                                ->columnSpanFull(),
                                        ])
                                        ->columnSpanFull(),
                                ]),
                            Tab::make('Requester Information')
                                ->schema([
                                    Group::make()
                                        ->schema([
                                            TextEntry::make('requester_name')
                                                ->label('Name')
                                                ->getStateUsing(function ($record) {
                                                    return $record->requester_last_name
                                                        . ', ' . $record->requester_first_name
                                                        . ' ' . substr($record->requester_middle_name,0, 1) . '.';
                                                })->columnSpan(4),

                                            TextEntry::make('cadet_serial_number')
                                                ->label('Serial Number')
                                                ->columnSpan(4),

                                            TextEntry::make('original_class')
                                                ->label('Original Class')
                                                ->columnSpan(4),

                                            TextEntry::make('address')
                                                ->label('Address')
                                                ->columnSpanFull()
                                                ->getStateUsing(function ($record) {
                                                    return $record->street_address .
                                                        ', ' . Province::where('province_id', $record->region)->first()->name .
                                                        ', ' . City::where('city_id', $record->city)?->first()->name .
                                                        ', ' . Barangay::find($record->barangay)?->name .
                                                        ', ' . $record->zip_code;
                                                })
                                        ])
                                        ->columns(12)
                                        ->columnSpan(12),
                                ]),
                            Tab::make('Representative Information')
                                ->schema([
                                    Group::make()
                                        ->schema([
                                            TextEntry::make('has_representative')
                                                ->label('I WILL COLLECT THE DOCUMENTS MYSELF')
                                                ->columnSpanFull()
                                                ->visible(fn($record) => ! $record->has_representative),

                                            TextEntry::make('representative_name')
                                                ->label('Name')
                                                ->getStateUsing(function ($record) {
                                                    return $record->representative_last_name
                                                        . ', ' . $record->representative_first_name
                                                        . ' ' . substr($record->representative_middle_name,0, 1) . '.';
                                                })->columnSpanFull()
                                                ->visible(fn($record) => $record->has_representative),

                                            TextEntry::make('representative_office')
                                                ->label('Office')
                                                ->columnSpan(6)
                                                ->visible(fn($record) => $record->has_representative),

                                            TextEntry::make('representative_contact')
                                                ->label('Office')
                                                ->columnSpan(6)
                                                ->visible(fn($record) => $record->has_representative),
                                        ])
                                        ->columns(12)
                                        ->columnSpanFull(),
                                ]),
                            Tab::make('Mailing Information')
                                ->schema([
                                    Group::make()
                                        ->schema([
                                            TextEntry::make('is_mailing')
                                                ->label('I WILL BE PICKING UP THE DOCUMENT/S')
                                                ->columnSpanFull()
                                                ->getStateUsing(fn () => '')
                                                ->visible(fn ($record) => ! $record->is_mailing),

                                            Group::make()
                                                ->relationship('deliveryDetail')
                                                ->schema([
                                                    TextEntry::make('recipient_name')
                                                        ->label('Name')
                                                        ->getStateUsing(function ($record) {
                                                            return $record?->recipient_last_name
                                                                . ', ' . $record?->recipient_first_name
                                                                . ' ' . substr($record?->recipient_middle_name,0, 1) . '.';
                                                        })->columnSpan(7),


                                                    TextEntry::make('recipient_contact_number')
                                                        ->label('Contact Number')
                                                        ->columnSpan(5),

                                                    TextEntry::make('address')
                                                        ->label('Address')
                                                        ->columnSpanFull()
                                                        ->getStateUsing(function ($record) {
                                                            return $record?->street_address .
                                                                ', ' . Province::where('province_id', $record?->region)?->first()?->name .
                                                                ', ' . City::where('city_id', $record?->city)?->first()?->name .
                                                                ', ' . Barangay::find($record?->barangay)?->name .
                                                                ', ' . $record?->zip_code;
                                                        })
                                                ])
                                                ->visible(fn ($record) => $record->is_mailing)
                                                ->columnSpanFull()
                                                ->columns(12),
                                        ])
                                        ->columns(12)
                                        ->columnSpanFull(),
                                ])
                            ])
                            ->columns(12)
                            ->columnSpanFull()

                    ])
                    ->columns(12)
                    ->columnSpanFull()
            ]);
    }
}
