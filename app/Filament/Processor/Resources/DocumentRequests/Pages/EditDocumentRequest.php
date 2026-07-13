<?php

namespace App\Filament\Processor\Resources\DocumentRequests\Pages;

use App\Filament\Processor\Resources\DocumentRequests\DocumentRequestResource;
use BalisMatz\FilamentPreventOutdatedRecordUpdate\Concerns\PreventsOutdatedRecordUpdate;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDocumentRequest extends EditRecord
{
    use PreventsOutdatedRecordUpdate;

    protected static string $resource = DocumentRequestResource::class;

    public static bool $formActionsAreSticky = true;
    public function getHeading(): string
    {
        return 'Edit Request';
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $raw = $this->form->getRawState();

        foreach ([
                     'representative_first_name',
                     'representative_last_name',
                     'representative_middle_name',
                     'representative_suffix',
                     'representative_office',
                     'representative_contact',
                 ] as $field) {
            $data[$field] = $raw[$field] ?? null;
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
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

}
