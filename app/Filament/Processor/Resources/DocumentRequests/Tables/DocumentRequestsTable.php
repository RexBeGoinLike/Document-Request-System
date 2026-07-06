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
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
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
                    ->badge(),

                TextColumn::make('reference_id')
                    ->label('Reference No.')
                    ->searchable(),

                TextColumn::make('requester_name')
                    ->label('Name')
                    ->getStateUsing(function ($record) {
                        return $record->requester_last_name
                            . ', ' . $record->requester_first_name
                            . ' ' . substr($record->requester_middle_name,0, 1) . '.';
                    })
                    ->searchable(query: function ($query, string $search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('requester_first_name', 'like', "%{$search}%")
                                ->orWhere('requester_middle_name', 'like', "%{$search}%")
                                ->orWhere('requester_last_name', 'like', "%{$search}%")
                                ->orWhere('requester_suffix', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('cadet_serial_number')
                    ->label('Serial Number')
                    ->searchable(),

                TextColumn::make('user_id')
                    ->label('Processor')
                    ->getStateUsing(fn ($record): string => $record?->processor->name ?? 'Not Assigned'),

            ])
            ->filters([
                Filter::make('status')
                    ->schema([
                        ToggleButtons::make('status')
                            ->options(DocumentRequestStatus::class)
                            ->inline()
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['status'],
                        fn (Builder $query, $status) => $query->where('status', $status),
                    ))
                    ->indicateUsing(fn (array $data): ?string => $data['status']
                        ? 'Status: ' . DocumentRequestStatus::from($data['status'])->getLabel()
                        : null)
                    ->columnSpanFull(),

                DateRangeFilter::make('created_at')
                    ->label('Created Within')
                    ->columnSpan(2),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
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
