<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Tables;

use App\DocumentRequestStatus;
use App\Filament\Exports\DocumentRequestExporter;
use App\Models\RequestQuantity;
use CodeWithKyrian\FilamentDateRange\Tables\Filters\DateRangeFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
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
use Illuminate\Support\Collection;

class DocumentRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->label('Export to Excel')
                    ->exporter(DocumentRequestExporter::class)
                    ->accessSelectedRecords()
                    ->icon('heroicon-o-table-cells')
                    ->formats([ExportFormat::Xlsx]),

                CreateAction::make('Create')
                    ->icon('heroicon-o-plus')
                    ->label('New Request')
            ])
            ->columns([
                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('last_status_modified_by')
                    ->label('Last Status Update')
                    ->badge()
                    ->getStateUsing(fn ($record): string => $record?->lastStatusModifiedBy->name ?? 'No Activity')
                    ->color(fn ($state): string => $state === 'No Activity' ? 'danger' : 'success')
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

                TextColumn::make('created_at')
                    ->label('Request Date')
                    ->sortable()

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
                    ->indicateUsing(fn (array $data): ?string => $data['status']
                        ? 'Status: ' . DocumentRequestStatus::from($data['status'])->getLabel()
                        : null)
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['status'],
                        function (Builder $query, $status) {
                            return $query->where('status', $status);
                        }
                    ))
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
                            ->default('All')
                            ->inline()
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['processor'],
                        function (Builder $query, $status) {

                        if ($status === 'Assigned') {
                            return $query->whereNotNull('last_field_modified_by');
                        } elseif ($status === 'Not Assigned') {
                            return $query->whereNull('last_field_modified_by');
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
