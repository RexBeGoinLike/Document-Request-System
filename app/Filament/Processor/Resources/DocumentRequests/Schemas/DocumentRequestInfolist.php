<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;

class DocumentRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Requester Information')
                                    ->schema([
                                        TextEntry::make('requester_name')
                                            ->label('Name')
                                            ->getStateUsing(function ($record) {
                                                return $record->requester_last_name
                                                    . ', ' . $record->requester_first_name
                                                    . ' ' . substr($record->requester_middle_name,0, 1) . '.';
                                            })->columnSpanFull(),

                                        TextEntry::make('cadet_serial_number')
                                            ->label('Serial Number')
                                            ->columnSpan(6),

                                        TextEntry::make('original_class')
                                            ->label('Original Class')
                                            ->columnSpan(6),

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
                                    ->columnSpanFull(),

                                Section::make('Representative Information')
                                    ->schema([
                                        TextEntry::make('has_representative')
                                            ->label('I WILL COLLECT THE DOCUMENTS MYSELF')
                                            ->columnSpanFull()
                                            ->visible(fn($record) => empty($record->representative_last_name)),

                                        TextEntry::make('representative_name')
                                            ->label('Name')
                                            ->getStateUsing(function ($record) {
                                                return $record->representative_last_name
                                                    . ', ' . $record->representative_first_name
                                                    . ' ' . substr($record->representative_middle_name,0, 1) . '.';
                                            })->columnSpanFull()
                                            ->visible(fn($record) => !empty($record->representative_last_name)),

                                        TextEntry::make('representative_office')
                                            ->label('Office')
                                            ->columnSpan(6)
                                            ->visible(fn($record) => !empty($record->representative_last_name)),

                                        TextEntry::make('representative_contact')
                                            ->label('Office')
                                            ->columnSpan(6)
                                            ->visible(fn($record) => !empty($record->representative_last_name)),
                                    ])
                                    ->columns(12)
                                    ->columnSpanFull(),

                                Section::make('Mailing Information')
                                    ->relationship('deliveryDetail')
                                    ->schema([
                                        TextEntry::make('is_self_collection')
                                            ->label('I WILL BE PICKING UP THE DOCUMENT/S')
                                            ->columnSpanFull()
                                            ->visible(fn($record) => empty($record->recipient_first_name)),

                                        TextEntry::make('recipient_name')
                                            ->label('Name')
                                            ->getStateUsing(function ($record) {
                                                return $record->recipient_last_name
                                                    . ', ' . $record->recipient_first_name
                                                    . ' ' . substr($record->recipient_middle_name,0, 1) . '.';
                                            })->columnSpan(6)
                                            ->visible(fn($record) => !empty($record->recipient_first_name)),

                                        TextEntry::make('recipient_contact_number')
                                            ->label('Contact Number')
                                            ->columnSpan(6)
                                            ->visible(fn($record) => !empty($record->recipient_first_name)),

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
                                            ->visible(fn($record) => !empty($record->recipient_first_name)),



                                    ])
                                    ->columns(12)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(5  )
                    ])
                    ->columnSpanFull()
                    ->columns(12)
            ]);
    }
}
