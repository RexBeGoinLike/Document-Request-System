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
            Action::make('back')
                ->label('Back to List')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),

            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

}
