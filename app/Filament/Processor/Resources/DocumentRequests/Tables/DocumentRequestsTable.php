<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Tables;

use App\DocumentRequestStatus;
use CodeWithKyrian\FilamentDateRange\Tables\Filters\DateRangeFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DocumentRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->formatStateUsing(fn (string $state) => strtoupper($state)),
                TextColumn::make('request_id')
                    ->label('Reference No.')
                    ->searchable(),
                TextColumn::make('requester_name')
                    ->label('Name')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->requester_last_name
                            . ', ' . $record->requester_first_name
                            . ' ' . substr($record->requester_middle_name,0, 1) . '.';
                    }),
                TextColumn::make('cadet_serial_number')
                    ->label('Serial Number')
                    ->searchable()

            ])
            ->filters([
                SelectFilter::make('status')
                ->options(
                    DocumentRequestStatus::class
                )->columnSpan(1),
                DateRangeFilter::make('created_at')
                    ->label('Created Within')
                    ->columnSpan(2),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->deferFilters(false)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
