<?php

namespace App\Filament\Processor\Resources\Users\Tables;

use App\Filament\Processor\Resources\Users\Widgets\LatestActivity;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class   UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextEntry::make('name')
                            ->columnSpan(5),

                        TextEntry::make('email')
                            ->label('Email address')
                            ->columnSpan(5),


                        TextEntry::make('role')
                        ->columnSpan(2),



                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-')
                            ->columnSpan(5),

                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-')
                            ->columnSpan(5),
                    ])
                    ->columnSpanFull()
                    ->columns(12),

                Livewire::make(LatestActivity::class,
                    fn ($record) => ['user_id' => $record->user_id])
                ->columnSpanFull()
            ]);
    }
}
