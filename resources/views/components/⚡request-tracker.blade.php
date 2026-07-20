<?php

use App\Filament\Processor\Resources\DocumentRequests\DocumentRequestResource;
use App\Models\DocumentRequest;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;

new class extends Component implements HasActions, HasSchemas {
    use InteractsWithActions;
    use InteractsWithSchemas;

    public string $tracking_number = '';

    public function submitSearch(): void
    {
        if (blank($this->tracking_number)) {
            return;
        }

        $record = DocumentRequest::where(
            'reference_id',
            trim($this->tracking_number)
        )->first();

        if (!$record) {
            Notification::make()
                ->danger()
                ->title('No request found.')
                ->send();

            return;
        }

        $this->reset('tracking_number');

        $this->replaceMountedAction(
            'viewRequestStatus',
            arguments: [
                'record' => $record->getKey(),
            ],
        );
    }

    public function viewRequestStatusAction(): Action
    {
        return Action::make('viewRequestStatus')
            ->modalHeading('Request Status')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->record(fn(array $arguments) => DocumentRequest::find($arguments['record']))
            ->schema(fn($schema) => DocumentRequestResource::infolist($schema));
    }
};
?>

<div class="flex items-center gap-x-3 sm:w-100">
    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Track a request:
    </span>
    <div class="relative grow group">
        <div
            class="absolute inset-y-0 left-0 flex items-center
            pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
            <x-filament::icon
                icon="heroicon-m-magnifying-glass"
                class="h-5 w-5"
            />
        </div>

        <input
            type="text"
            wire:model="tracking_number"
            wire:keydown.enter="submitSearch"
            placeholder="Enter 11 Digit Reference Number..."
            class="block w-full pl-10 pr-3 py-1.5 text-xs rounded-lg
                    bg-gray-50 dark:bg-white/5
                    border border-gray-300 dark:border-white/10
                    shadow-sm
                    text-gray-900 dark:text-white placeholder-gray-400
                    focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20
                    outline-none transition duration-75"
        />
    </div>

    <!-- Required modal layout compiler context -->
    <x-filament-actions::modals/>
</div>
