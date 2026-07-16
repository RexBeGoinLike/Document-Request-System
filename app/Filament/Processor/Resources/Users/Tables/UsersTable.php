<?php

namespace App\Filament\Processor\Resources\Users\Tables;

use App\DocumentRequestStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('Last Activity')
                    ->label('Last Activity')
                    ->getStateUsing(fn ($record)
                    => Activity::where('causer_id', $record->user_id)?->first()?->created_at),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn($state) => $state == 'processor' ? 'primary' : 'danger')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('role')
                    ->schema([
                        ToggleButtons::make('role')
                            ->label('Role')
                            ->default('all')
                            ->grouped()
                            ->options([
                                'all' => 'All',
                                'admin' => 'Admin',
                                'processor' => 'Processor',
                            ])
                            ->inline()
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['role'],
                        fn (Builder $query, $role) => $role == 'all' ? $query : $query->where('role', $role),
                    ))
                    ->columnSpanFull()
            ])
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
