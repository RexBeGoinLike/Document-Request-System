<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Tables;


use App\DocumentRequestStatus;
use ArielMejiaDev\FilamentPrintable\Actions\PrintAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
use Relaticle\ActivityLog\Filament\Infolists\Components\ActivityLog;
use App\Models\DocumentType;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
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
                                Flex::make([
                                    TextEntry::make('reference_id')
                                        ->hiddenLabel()
                                        ->size('lg')
                                        ->badge()
                                        ->color('primary')
                                        ->icon('heroicon-o-hashtag')
                                        ->grow(false),

                                    TextEntry::make('type')
                                        ->hiddenLabel()
                                        ->size('lg')
                                        ->badge()
                                        ->icon(fn ($record): string => $record?->is_mailing ? 'heroicon-o-envelope' : 'heroicon-o-archive-box')
                                        ->color('gray')
                                        ->getStateUsing(fn ($record): string => $record?->is_mailing ? 'FOR MAILING' : 'SELF COLLECTION')
                                        ->grow(false),

                                    TextEntry::make('status')
                                        ->hiddenLabel()
                                        ->size('lg')
                                        ->badge()
                                        ->columnSpanFull(),
                                    /*
                                    TextEntry::make('user_id')
                                        ->formatStateUsing(fn ($record) => ($record->user_id) ? $record->processor->name : 'No Activity')
                                        ->hiddenLabel()
                                        ->badge()
                                        ->size('lg')
                                        ->icon('heroicon-o-user')
                                        ->color(fn ($record) => ($record->user_id) ? 'success' : 'danger'),*/

                                    Action::make('back')
                                        ->label('Back to List')
                                        ->icon('heroicon-o-chevron-left')
                                        ->color('gray')
                                        ->url('/processor/document-requests'),

                                    EditAction::make('Edit')
                                        ->icon('heroicon-o-pencil'),

                                    Action::make('History')
                                        ->icon('heroicon-o-clock')
                                        ->schema([
                                            ActivityLog::make('activity')
                                                ->columnSpanFull()
                                                ->perPage(3)
                                                ->groupByDate()
                                                ->emptyState('Nothing here yet — give it time.'),
                                        ]),
                                ])
                                ->columnSpanFull()
                                ->from('sm'),

                                Section::make('Transaction Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->headerActions([
                                        Action::make('View Letter')
                                            ->label('View Letter')
                                            ->modalHeading(false)
                                            ->icon('heroicon-o-eye')
                                            ->schema([
                                                PdfViewerEntry::make('representative.authorization_letter')
                                                    ->hiddenLabel()
                                                    ->disk('attachments') //Always Defaults to Storage IF YOU PLAN TO STORE FILES USE THE STORAGE ROUTE IN WEB.PHP
                                                    ->minHeight('80svh')
                                                    ->columnSpan(12)
                                            ])
                                            ->modalSubmitAction(false)
                                            ->modalCancelAction(false)
                                            ->visible(fn ($record) => $record?->representative?->authorization_letter ?? false),


                                        PrintAction::make()

                                    ])
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Section::make('Cadet Information')
                                                    ->compact()
                                                    ->schema([

                                                        TextEntry::make('name')
                                                            ->label('Cadet Name')
                                                            ->getStateUsing(function ($record) {
                                                                return $record->last_name
                                                                    . ', ' . $record->first_name
                                                                    . ' ' . $record->middle_name .
                                                                    ' ' . $record->suffix;
                                                            })
                                                            ->columnSpan(5),

                                                        TextEntry::make('serial_number')
                                                            ->label('Serial No.')
                                                            ->columnSpan(2),

                                                        TextEntry::make('class')
                                                            ->label('Class')
                                                            ->columnSpan(2),

                                                        TextEntry::make('contact_number')
                                                            ->columnSpan(3),
                                                    ])
                                                    ->columns(12)
                                                    ->columnSpan(12),

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
                                                                            DocumentType::where('id', $record->document_type_id)->first()->document_type;
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

                                                TextEntry::make('amount_payable')
                                                    ->label('Total Fee')
                                                    ->inlineLabel()
                                                    ->numeric(decimalPlaces: 0)
                                                    ->prefix('PHP ')
                                                    ->columnSpanFull()
                                            ])
                                            ->columnSpan(7)
                                            ->columns(12),

                                        Group::make()
                                            ->schema([
                                                Section::make('Representative')
                                                    ->compact()
                                                    ->schema([

                                                        TextEntry::make('has_representative')
                                                            ->label('I WILL COLLECT THE DOCUMENTS MYSELF')
                                                            ->columnSpanFull()
                                                            ->visible(fn($record) => ! $record->has_representative),

                                                        TextEntry::make('name')
                                                            ->label('Name')
                                                            ->getStateUsing(function ($record) {
                                                                return $record?->representative?->last_name
                                                                    . ', ' . $record?->representative?->first_name
                                                                    . ' ' . $record?->representative?->middle_name.
                                                                    ' ' . $record?->representative?->suffix;
                                                            })
                                                            ->columnSpan(8)
                                                            ->visible(fn($record) => $record->has_representative),

                                                        TextEntry::make('representative.relationship')
                                                            ->label('Relationship')
                                                            ->columnSpan(4)
                                                            ->visible(fn($record) => $record->has_representative),

                                                        TextEntry::make('representative.office')
                                                            ->label('Office')
                                                            ->columnSpan(6)
                                                            ->visible(fn($record) => $record->has_representative),

                                                        TextEntry::make('representative.contact')
                                                            ->label('Contact')
                                                            ->columnSpan(6)
                                                            ->visible(fn($record) => $record->has_representative),
                                                    ])
                                                    ->columns(12)
                                                    ->columnSpan(12),

                                                Section::make('Mailing Information')
                                                    ->compact()
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
                                                                        TextEntry::make('deliveryDetail.tracking_number')
                                                                            ->columnSpan(7)
                                                                            ->badge()
                                                                            ->visible(fn ($record) => $record->deliveryDetail->courier_name ?? false),

                                                                        TextEntry::make('deliveryDetail.courier_name')
                                                                            ->badge()
                                                                            ->columnSpan(5)
                                                                            ->visible(fn ($record) => $record->deliveryDetail->courier_name ?? false),

                                                                        TextEntry::make('deliveryDetail.name')
                                                                            ->label('Name')
                                                                            ->getStateUsing(function ($record) {
                                                                                $delivery = $record->deliveryDetail;
                                                                                if (! $delivery) {
                                                                                    return null;
                                                                                }

                                                                                return $delivery->last_name
                                                                                    . ', ' . $delivery->first_name
                                                                                    . ' ' . $delivery->middle_name .
                                                                                    ' ' . $delivery->suffix;
                                                                            })
                                                                            ->columnSpan(7),

                                                                        TextEntry::make('deliveryDetail.contact_number')
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
                                                                                    ', ' . $delivery->region .
                                                                                    ', ' .$delivery->city .
                                                                                    ', ' . $delivery->barangay .
                                                                                    ', ' . $delivery->zip_code;
                                                                            }),


                                                                    ])
                                                                    ->visible(fn ($record) => $record->is_mailing)
                                                                    ->columnSpanFull()
                                                                    ->columns(12),
                                                            ])
                                                            ->columns(12)
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->columnSpanFull()
                                                    ->columns(12)
                                    ])
                                            ->columnSpan(5)
                                            ->columns(12),
                                    ])
                                    ->columns(12)
                                    ->columnSpan(12),
                            ])
                            ->columnSpanFull()
                            ->columns(12),
            ]);
    }
}
