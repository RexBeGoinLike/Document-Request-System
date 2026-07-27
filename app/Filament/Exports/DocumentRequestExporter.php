<?php

namespace App\Filament\Exports;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class DocumentRequestExporter extends Exporter
{
    protected static ?string $model = DocumentRequest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('reference_id'),

            ExportColumn::make('serial_number'),

            ExportColumn::make('last_name')
                ->label('Last Name'),

            ExportColumn::make('first_name')
                ->label('First Name'),

            ExportColumn::make('middle_name')
                ->label('Middle Name'),

            ExportColumn::make('suffix')
                ->label('Suffix'),

            ExportColumn::make('request_quantities')
                ->label('Requested Documents')
                ->state(function ($record) {
                    return $record->requestQuantity
                        ->map(function ($item) {
                            return sprintf(
                                '%s (₱%s)',
                                $item->documentType?->document_type ?? $item->document_type_id,
                                number_format($item->total_fee, 2)
                            );
                        })
                        ->implode(", ");
                }),

            ExportColumn::make('created_at')
                ->label('Request Date'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your document request export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
