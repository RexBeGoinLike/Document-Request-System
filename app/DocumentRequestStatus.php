<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum DocumentRequestStatus : string implements HasLabel, HasColor, HasIcon
{
    case FOR_REVIEW = 'for_review';

    case REJECTED = 'rejected';
    case APPROVED = 'approved';

    case FOR_COLLECTION = 'for_collection';
    case COLLECTED = 'collected';
    case FOR_DISPATCH = 'for_dispatch';
    case DISPATCHED = 'dispatched';
    case DELIVERED = 'delivered';

    public function getLabel(): string|Htmlable|null
    {
       return match ($this) {
           self::FOR_REVIEW => 'FOR REVIEW',
           self::APPROVED => 'APPROVED',
           self::REJECTED => 'REJECTED',
           self::FOR_COLLECTION => 'FOR COLLECTION',
           self::COLLECTED => 'COLLECTED',
           self::FOR_DISPATCH => 'FOR DISPATCH',
           self::DISPATCHED => 'DISPATCHED',
           self::DELIVERED => 'DELIVERED',
           default => null,
       };
    }

    public function getColor(): string|null
    {
        return match ($this) {
            self::FOR_REVIEW => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::FOR_COLLECTION => 'warning',
            self::COLLECTED => 'success',
            self::FOR_DISPATCH => 'warning',
            self::DISPATCHED => 'primary',
            self::DELIVERED => 'success',
            default => null,
        };
    }

    public function getIcon(): string|null
    {
        return match ($this) {
            self::FOR_REVIEW => 'heroicon-o-clock',
            self::APPROVED => 'heroicon-o-hand-thumb-up',
            self::REJECTED => 'heroicon-o-hand-thumb-down',
            self::FOR_COLLECTION => 'heroicon-o-information-circle',
            self::COLLECTED => 'heroicon-o-check-circle',
            self::FOR_DISPATCH => 'heroicon-o-paper-airplane',
            self::DISPATCHED => 'heroicon-o-truck',
            self::DELIVERED => 'heroicon-o-check-circle',
        };
    }
}
