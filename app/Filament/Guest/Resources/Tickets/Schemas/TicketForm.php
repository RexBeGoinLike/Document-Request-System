<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas;

use App\Filament\Guest\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateDocumentType;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateMailingInformation;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateRepresentativeInformation;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateRequesterInformation;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateSummary;
use App\Models\DocumentType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;
use function Laravel\Prompts\alert;


class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([

                    CreateRequesterInformation::make(),

                    CreateDocumentType::make(),

                    CreateRepresentativeInformation::make(),

                    CreateMailingInformation::make()
                    ->afterValidation(
                        function (Get $get, Set $set, $livewire) {
                            $errors = $livewire->getErrorBag();

                            if ($errors->isNotEmpty()) {
                                throw new Halt();
                            }

                            //REQUESTOR
                            $set('s_requester_name',
                                sprintf("%s, %s %s %s",
                                    $get('requester_last_name'),
                                    $get('requester_first_name'),
                                    $get('requester_suffix'),
                                    $get('requester_middle_name')));
                            $set('s_cadet_serial_number', $get('cadet_serial_number'));
                            $set('s_original_class', $get('original_class'));
                            $set('s_contact_number', $get('contact_number'));
                            $set('s_requester_address',
                                sprintf("%s, %s, %s, %s, %d",
                                    $get('street_address'),
                                    Barangay::find($get('barangay'))->name,
                                    City::where('city_id', $get('city'))?->first()->name,
                                    Province::where('province_id', $get('region'))?->first()->name,
                                    $get('zip_code')));


                            //SELF COLLECTION
                            $self_collect = $get('self_collect');

                            if (! $self_collect) {
                                $set('s_representative_full_name',
                                    sprintf("%s, %s %s %s",
                                        $get('representative_last_name'),
                                        $get('representative_first_name'),
                                        $get('representative_suffix'),
                                        $get('representative_middle_name')));
                                $set('s_representative_office', $get('representative_office'));
                                $set('s_representative_contact', $get('representative_contact'));
                            }else{
                                $set('s_rep_selected', $self_collect);
                            }

                            //MAILING
                            $for_mailing = $get('mailing_information.for_mailing');
                            alert($for_mailing);
                            $set('s_mailing_selected', $for_mailing);

                            if ($for_mailing) {
                                $set('s_mailing_full_name',
                                    sprintf("%s, %s %s %s",
                                        $get('mailing_information.recipient_last_name'),
                                        $get('mailing_information.recipient_first_name'),
                                        $get('mailing_information.recipient_suffix'),
                                        $get('mailing_information.recipient_middle_name')));
                                $set('s_mailing_full_address',
                                    sprintf("%s, %s, %s, %s, %d",
                                    $get('mailing_information.street_address'),
                                        Barangay::find($get('mailing_information.barangay'))->name,
                                        City::find($get('mailing_information.city'))->name,
                                        Province::find($get('mailing_information.region'))->name,
                                        $get('mailing_information.zip_code')));
                                $set('s_mailing_contact', $get('mailing_information.recipient_contact_number'));
                            }
                        }
                    ),

                    CreateSummary::make(),

                ])
                    ->columnSpanFull()
                    ->skippable(false)
                    ->submitAction(new HtmlString(Blade::render('
                            <x-filament::button type="submit" size="sm">
                                Submit Form
                            </x-filament::button>
                        '))),
            ]);
    }
}
