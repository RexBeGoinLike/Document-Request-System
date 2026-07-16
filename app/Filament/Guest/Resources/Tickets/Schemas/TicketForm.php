<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas;

use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateDocumentType;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateMailingInformation;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateRepresentativeInformation;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateRequesterInformation;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateSummary;
use App\Models\DocumentType;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Province;


class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    CreateDocumentType::make(),

                    CreateRequesterInformation::make(),

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
                                $has_representative = $get('has_representative');

                                if ($has_representative) {
                                    $set('s_rep_selected', true);
                                    $set('s_representative_full_name',
                                        sprintf("%s, %s %s %s",
                                            $get('representative_last_name'),
                                            $get('representative_first_name'),
                                            $get('representative_suffix'),
                                            $get('representative_middle_name')));
                                    $set('s_representative_office', $get('representative_office'));
                                    $set('s_representative_contact', $get('representative_contact'));
                                }else{
                                    $set('s_rep_selected', false);
                                }


                                $is_mailing = $get('is_mailing');
                                $set('s_mailing_selected', $is_mailing);

                                if ($is_mailing) {
                                    $set('s_mailing_full_name',
                                        sprintf("%s, %s %s %s",
                                            $get('deliveryDetail.recipient_last_name'),
                                            $get('deliveryDetail.recipient_first_name'),
                                            $get('deliveryDetail.recipient_suffix'),
                                            $get('deliveryDetail.recipient_middle_name')));
                                    $set('s_mailing_full_address',
                                        sprintf("%s, %s, %s, %s, %d",
                                            $get('deliveryDetail.street_address'),
                                            Barangay::find($get('deliveryDetail.barangay'))->name,
                                            City::where('city_id',$get('deliveryDetail.city'))->first()->name,
                                            Province::where('province_id', $get('deliveryDetail.region'))->first()->name,
                                            $get('deliveryDetail.zip_code')));
                                    $set('s_mailing_contact', $get('deliveryDetail.recipient_contact_number'));
                                }

                                \Log::info('=== FEE BREAKDOWN DEBUG ===', [
                                    'full_data' => $livewire->data ?? 'NO DATA PROPERTY',
                                ]);

                                //FEE BREAKDANCE
                                $set('s_fee_breakdown', $get('fee_breakdown'));

                                $items = $get('fee_breakdown') ?? [];

                                $total = collect($items)->sum('total_fee');

                                $set('s_fee_breakdown_doc', $total);
                                $set('s_mailing_fee', DocumentType::where('document_type_id', '15')->first()->document_fee);
                                $set('s_total_fee', ($is_mailing) ? $total + 200 : $total);
                                $set('f_total_fee', ($is_mailing) ? $total + 200 : $total);
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
