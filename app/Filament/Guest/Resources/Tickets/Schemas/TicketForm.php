<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas;

use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateDocumentType;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateMailingInformation;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateRepresentativeInformation;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateRequesterInformation;
use App\Filament\Guest\Resources\Tickets\Schemas\WizardSteps\CreateSummary;
use App\Models\DocumentType;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
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
                    Step::make('Welcome')
                        ->label('Start')
                        ->icon('heroicon-o-hand-raised')
                        ->schema([
                            Text::make(new HtmlString('
                                <div style="
                                    width: 100%;
                                    min-height: 60vh;
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    justify-content: center;
                                    text-align: center;
                                    background-image:
                                    linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                                    url(\'' . asset('css/images/guest_bg.jpg') . '\');
                                    background-size: cover;
                                    background-position: center;
                                    border-radius: 12px;
                                    padding: 10px;
                                   ">
                                   <img src="' . asset('css/images/pma_logo.svg') . '" style="height: 100px; width: auto;">
                                    <h1 style="font-size: 64px; font-weight: bold; color: white; text-shadow: 0 2px 8px rgba(0,0,0,0.6);">
                                        Office of the Registrar
                                    </h1>
                                    <p style="font-size: 32px; color: white; text-shadow: 0 2px 8px rgba(0,0,0,0.6); margin-top: 12px;">
                                        Tap the "Next" below to start the request form.
                                    </p>
                                </div>
                            '))
                                ->extraAttributes(['class' => 'w-full']),
                        ]),


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
                                $set('s_full_name',
                                    sprintf("%s, %s %s %s",
                                        $get('last_name'),
                                        $get('first_name'),
                                        $get('suffix'),
                                        $get('middle_name')));
                                $set('s_serial_number', $get('serial_number'));
                                $set('s_class', $get('class'));
                                $set('s_contact_number', $get('contact_number'));
                                $set('s_address',
                                    sprintf("%s, %s, %s, %s, %d",
                                        $get('street_address'),
                                        $get('barangay'),
                                        $get('city'),
                                        $get('region'),
                                        $get('zip_code')));


                                //SELF COLLECTION
                                $has_representative = $get('has_representative');

                                if ($has_representative) {
                                    $set('s_rep_selected', true);
                                    $set('s_representative_full_name',
                                        sprintf("%s, %s %s %s",
                                            $get('representative.last_name'),
                                            $get('representative.first_name'),
                                            $get('representative.suffix'),
                                            $get('representative.middle_name')));
                                    $set('s_office', $get('representative.office'));
                                    $set('s_contact_number', $get('representative.contact'));
                                    $set('s_relationship', $get('representative.relationship'));
                                }else{
                                    $set('s_rep_selected', false);
                                }


                                $is_mailing = $get('is_mailing');
                                $set('s_mailing_selected', $is_mailing);

                                if ($is_mailing) {
                                    $set('s_mailing_full_name',
                                        sprintf("%s, %s %s %s",
                                            $get('deliveryDetail.last_name'),
                                            $get('deliveryDetail.first_name'),
                                            $get('deliveryDetail.suffix'),
                                            $get('deliveryDetail.middle_name')));
                                    $set('s_mailing_full_address',
                                        sprintf("%s, %s, %s, %s, %d",
                                            $get('deliveryDetail.street_address'),
                                            $get('deliveryDetail.barangay'),
                                            $get('deliveryDetail.city'),
                                            $get('deliveryDetail.region'),
                                            $get('deliveryDetail.zip_code')));
                                    $set('s_mailing_contact', $get('deliveryDetail.contact_number'));
                                }

                                \Log::info('=== FEE BREAKDOWN DEBUG ===', [
                                    'full_data' => $livewire->data ?? 'NO DATA PROPERTY',
                                ]);

                                //FEE BREAKDANCE
                                $set('s_fee_breakdown', $get('fee_breakdown'));

                                $items = $get('fee_breakdown') ?? [];

                                $total = collect($items)->sum('total_fee');

                                $set('s_fee_breakdown_doc', $total);
                                $set('s_mailing_fee', DocumentType::where('id', '15')->first()->document_fee);

                                $totalFee = ($is_mailing) ? $total + 200 : $total;
                                $set('s_total_fee', $totalFee);
                                $set('amount_payable', $totalFee);
                            }
                        ),

                    CreateSummary::make(),

                ])
                    ->hiddenHeader(/*fn (Wizard $wizard) => $wizard->getCurrentStepIndex() === 0*/)
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
