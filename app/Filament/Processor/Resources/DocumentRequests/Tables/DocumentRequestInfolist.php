<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Tables;


use App\Models\DocumentType;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Torgodly\Html2Media\Actions\Html2MediaAction;
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
                            ->hiddenLabel()
                            ->badge()
                            ->size('lg')
                            ->columnSpanFull(),

                        Flex::make([
                                TextEntry::make('type')
                                    ->hiddenLabel()
                                    ->badge()
                                    ->size('sm')
                                    ->icon(fn ($record): string => $record?->is_mailing ? 'heroicon-o-envelope' : 'heroicon-o-archive-box')
                                    ->getStateUsing(fn ($record): string => $record?->is_mailing ? 'For Mailing' : 'Self Collection')
                                    ->grow(false),

                                TextEntry::make('user_processor')
                                    ->hiddenLabel()
                                    ->getStateUsing(fn ($record): string => $record?->processor->name ?? 'Not Assigned')
                                    ->badge()
                                    ->size('sm')
                                    ->icon(fn ($state): string => $state === 'Not Assigned' ? 'heroicon-o-x-mark' : 'heroicon-o-user')
                                    ->color(fn ($state): string => $state === 'Not Assigned' ? 'danger' : 'success'),

                            Html2MediaAction::make('print')
                                ->content(fn($record) => view('filament.processor.pages.document-request-print', [
                                    'record' => $record,
                                ]))
                                ->hiddenLabel()
                                ->icon('heroicon-o-printer')
                                ->margins(10),
                            ])->columnSpanFull()->from('sm'),


                        Section::make('Requester Information')
                            ->schema([
                                TextEntry::make('requester_name')
                                    ->label('Name')
                                    ->getStateUsing(function ($record) {
                                        return $record->requester_last_name
                                            . ', ' . $record->requester_first_name
                                            . ' ' . $record->requester_middle_name .
                                            ' ' . $record->requester_suffix;
                                    })->columnSpan(4),

                                TextEntry::make('cadet_serial_number')
                                    ->label('Serial Number')
                                    ->columnSpan(4),

                                TextEntry::make('original_class')
                                    ->label('Original Class')
                                    ->columnSpan(4),

                                TextEntry::make('contact_number')
                                    ->label('Contact Number')
                                    ->columnSpan(2),

                                TextEntry::make('address')
                                    ->label('Address')
                                    ->columnSpan(10)
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

                                    TextEntry::make('mailing_fee')
                                        ->label('Mailing Fee')
                                        ->inlineLabel()
                                        ->prefix('PHP ')
                                        ->getStateUsing(fn()=>200)
                                        ->columnSpanFull(),

                                    TextEntry::make('f_total_fee')
                                        ->label('Total Fee')
                                        ->inlineLabel()
                                        ->numeric(decimalPlaces: 0)
                                        ->prefix('PHP ')
                                        ->columnSpanFull()
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
                                                        . ' ' . $record->representative_middle_name.
                                                        ' ' . $record->representative_suffix;
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
                                                    ->schema([
                                                        TextEntry::make('deliveryDetail.recipient_name')
                                                            ->label('Name')
                                                            ->getStateUsing(function ($record) {
                                                                $delivery = $record->deliveryDetail;
                                                                if (! $delivery) {
                                                                    return null;
                                                                }

                                                                return $delivery->recipient_last_name
                                                                    . ', ' . $delivery->recipient_first_name
                                                                    . ' ' . $delivery->recipient_middle_name .
                                                                    ' ' . $delivery->recipient_suffix;
                                                            })
                                                            ->columnSpan(7),

                                                        TextEntry::make('deliveryDetail.recipient_contact_number')
                                                            ->label('Contact Number')
                                                            ->columnSpan(5),

                                                        TextEntry::make('deliveryDetail.address')
                                                            ->label('Address')
                                                            ->columnSpanFull()
                                                            ->getStateUsing(function ($record) {
                                                                $delivery = $record->deliveryDetail;
                                                                if (! $delivery) {
                                                                    return null;
                                                                }

                                                                return $delivery->street_address .
                                                                    ', ' . Province::where('province_id', $delivery->region)->first()?->name .
                                                                    ', ' . City::where('city_id', $delivery->city)->first()?->name .
                                                                    ', ' . Barangay::find($delivery->barangay)?->name .
                                                                    ', ' . $delivery->zip_code;
                                                            }),

                                                        TextEntry::make('deliveryDetail.courier_name')
                                                            ->columnSpan(3)
                                                            ->visible(fn ($record) => $record->deliveryDetail->courier_name ?? false),

                                                        TextEntry::make('deliveryDetail.tracking_number')
                                                            ->columnSpan(4)
                                                            ->visible(fn ($record) => $record->deliveryDetail->courier_name ?? false),

                                                    ])
                                                    ->visible(fn ($record) => $record->is_mailing)
                                                    ->columnSpanFull()
                                                    ->columns(12),
                                            ])
                                            ->columns(12)
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columns(12)
                            ->columnSpanFull()

                    ])
                    ->columns(12)
                    ->columnSpanFull()
            ]);
    }
}
