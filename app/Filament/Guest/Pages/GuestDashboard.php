<?php

namespace App\Filament\Guest\Pages;

use App\Filament\Guest\Resources\Tickets\TicketResource;
use App\Filament\Processor\Resources\DocumentRequests\DocumentRequestResource;
use App\Filament\Processor\Resources\DocumentRequests\Tables\DocumentRequestInfolist;
use App\Models\DocumentRequest;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class GuestDashboard extends Page implements HasSchemas
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    protected string $view = 'filament.guest.pages.guest-dashboard';

    public ?array $data = [];
    protected static ?string $slug = '/';

    protected static ?string $title = '';

    public function getHeading(): string
    {
        return '';
    }

    public function choicesSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Section::make('Request a Document')
                            ->description('Diplomas, transcripts, certifications, and other cadet records.')
                            ->icon('heroicon-o-document-text')
                            ->footerActions([
                                Action::make('start_request')
                                    ->label('Start Request')
                                    ->url(TicketResource::getUrl('create'))
                                    ->button(),
                            ]),

                         Section::make('Track a Request')
                            ->description('Check the status of a document request using your ticket number.')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                TextInput::make('tracking_number')
                                    ->label('Tracking Number')
                                    ->live(onBlur: true)
                                    ->columnSpanFull()
                                    ->suffixAction(
                                        Action::make('track')
                                            ->label('Track')
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->modalHeading('Request Status')
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Close')
                                            ->record(function (Get $get) {
                                                return DocumentRequest::where('reference_id', $get('tracking_number'))->first();
                                            })
                                            ->schema(fn (Schema $schema) => DocumentRequestResource::infolist($schema))
                                            ->mountUsing(function (Get $get, Action$action) {
                                                $trackingNumber = $get('tracking_number');

                                                if (empty($trackingNumber)) {
                                                    Notification::make()
                                                        ->title('Please enter a tracking number.')
                                                        ->warning()
                                                        ->send();

                                                    $action->halt();
                                                }

                                                $exists = DocumentRequest::where('reference_id', $trackingNumber)->exists();

                                                if (! $exists) {
                                                    Notification::make()
                                                        ->title('No request found for that tracking number.')
                                                        ->danger()
                                                        ->send();

                                                    $action->halt();
                                                }
                                            })
                    ),
            ]),
                    ])
            ])->statePath('data');
    }

    public function viewRequestStatusAction(): Action
    {
        return Action::make('viewRequestStatus')
            ->modalHeading('Request Status')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->record(fn (array $arguments) => DocumentRequest::find($arguments['record']))
            ->schema(fn (Schema $schema) => DocumentRequestResource::infolist($schema));
    }
}
