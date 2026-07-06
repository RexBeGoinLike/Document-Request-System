<?php
namespace App\Filament\Processor\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ActivityLogs extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?int $navigationSort = 2;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-clock';

    protected string $view = 'filament.processor.pages.activity-logs';
    protected static ?string $navigationLabel = 'View Activity';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->latest())
            ->columns([
                TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—')
                    ->badge(),
                TextColumn::make('subject_id')->label('Record ID'),
                TextColumn::make('event')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('causer.name')->label('Caused By')->default('System'),
                TextColumn::make('created_at')->label('When')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('subject_type')
                    ->label('Model')
                    ->options(fn () => Activity::query()
                        ->distinct()
                        ->pluck('subject_type', 'subject_type')
                        ->mapWithKeys(fn ($type) => [$type => class_basename($type)])
                    ),

                SelectFilter::make('event')
                    ->label('Event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ]);
    }
}
