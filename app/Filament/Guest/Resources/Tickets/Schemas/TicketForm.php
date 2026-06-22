<?php

namespace App\Filament\Guest\Resources\Tickets\Schemas;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Wizard::make([
                    //STEP 1
                    Wizard\Step::make('Requestor Information')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Section::make()->schema([
                            TextInput::make('requester_name')
                                ->autocapitalize('words')
                                ->required(),

                            TextInput::make('cadet_serial_number')
                                ->autocapitalize('words')
                                ->required(),

                            TextInput::make('original_class')
                                ->autocapitalize('words')
                                ->required(),

                            TextInput::make('contact_number')
                                ->autocapitalize('characters')
                                ->tel()
                                ->required(),

                            TextInput::make('address')
                                ->autocapitalize('characters')
                                ->required(),
                        ])->columns(3)
                    ]),
                    Wizard\Step::make('Type of Document/s')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Section::make()->schema([

                            ])->columns(3)
                        ])
                ])->columnSpanFull()
            ]);
    }
}
