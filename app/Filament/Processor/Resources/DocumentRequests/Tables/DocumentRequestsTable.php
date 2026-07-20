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

                TextColumn::make('user_id')
                    ->label('Processor')
                    ->badge()
                    ->getStateUsing(fn ($record): string => $record?->processor->name ?? 'Not Assigned')
                    ->color(fn ($state): string => $state === 'Not Assigned' ? 'danger' : 'success')
                    ->sortable(),

                TextColumn::make('reference_id')
                    ->label('Reference No.')
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->getStateUsing(function ($record) {
                        return $record->last_name
                            . ', ' . $record->first_name
                            . ' ' . substr($record->middle_name,0, 1) . '. '
                            . $record->suffix;
                    })
                    ->searchable(query: function ($query, string $search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('middle_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('suffix', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                TextColumn::make('representative_name')
                ->label('Representative')
                ->getStateUsing(function ($record) {
                    return ($record->has_representative === true) ?
                        ($record?->representative?->last_name
                    . ', ' . $record?->representative?->first_name
                    . ' ' . substr($record?->representative?->middle_name, 0, 1)
                    . '. ' . $record?->representative?->suffix) : 'None';
                }),

                TextColumn::make('serial_number')
                    ->label('Serial')
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([


                Filter::make('status')
                    ->schema([
                        ToggleButtons::make('status')
                            ->options(DocumentRequestStatus::class)
                            ->inline()
                            ->extraAttributes(['class' => 'text-sm'])
                            ->grouped()

                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['status'],
                        fn (Builder $query, $status) => $query->where('status', $status),
                    ))
                    ->indicateUsing(fn (array $data): ?string => $data['status']
                        ? 'Status: ' . DocumentRequestStatus::from($data['status'])->getLabel()
                        : null)
                    ->columnSpanFull(),


                Filter::make('processor')
                    ->schema([
                        ToggleButtons::make('processor')
                            ->options([
                                'All' => 'All',
                                'Assigned' => 'Handled',
                                'Not Assigned' => 'Open'

                            ])
                            ->grouped()
                            ->default(auth()->user()->role === 'admin' ? 'All' : 'Assigned')
                            ->inline()
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['processor'],
                        function (Builder $query, $status) {
                            if(auth()->user()->role === 'admin')
                            {
                                if ($status === 'Assigned') {
                                    return $query->whereNotNull('user_id');
                                } elseif ($status === 'Not Assigned') {
                                    return $query->whereNull('user_id');
                                }
                            } else {
                                if ($status === 'Assigned') {
                                    return $query->where('user_id', auth()->user()->user_id);
                                } elseif ($status === 'Not Assigned') {
                                    return $query->where('user_id', '!=', auth()->user()->user_id);
                                }
                            }

                            return $query;
                        }
                    ))
                    ->columnSpan(4),

                DateRangeFilter::make('created_at')
                    ->label('Created Within')
                    ->columnSpan(8),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(12)
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
