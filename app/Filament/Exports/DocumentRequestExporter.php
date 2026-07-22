<?php

namespace App\Filament\Exports;

use App\Models\DocumentRequest;
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

            ExportColumn::make('requestQuantity.documentType.name')
                ->label('Document Type'),

            ExportColumn::make('requestQuantity.quantity'),

            ExportColumn::make('requestQuantity.fee')
                ->label('Fee (₱)'),

            ExportColumn::make('requestQuantity.total_fee')
                ->label('Total Fee (₱)'),

            ExportColumn::make('requestQuantity.created_at')
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
