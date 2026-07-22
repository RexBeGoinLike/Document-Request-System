<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Pages;

use App\DocumentRequestStatus;
use App\Filament\Processor\Resources\DocumentRequests\DocumentRequestResource;
use BalisMatz\FilamentPreventOutdatedRecordUpdate\Concerns\PreventsOutdatedRecordUpdate;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Utilities\Get;

class EditDocumentRequest extends EditRecord
{

    protected static string $resource = DocumentRequestResource::class;

    public static bool $formActionsAreSticky = true;
    public function getHeading(): string
    {
        return 'Edit Request';
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $raw = $this->form->getRawState();

        // 1. Get the current fresh database values for this record
        $record = $this->getRecord();

        $data['last_field_modified_by'] = auth()->id();


        if ($data['status'] !== $this->getRecord()->status) {
            $data['last_status_modified_by'] = auth()->id();
        }

        return $data;
    }


    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('heroicon-o-chevron-left')
                ->label('Back to View'),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

}
