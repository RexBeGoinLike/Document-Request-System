<?php

namespace App;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum DocumentRequestStatus : string implements HasLabel
{
    case FOR_REVIEW = 'for_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FOR_COLLECTION = 'for_collection';
    case COLLECTED = 'collected';
    case FOR_DISPATCH = 'for_dispatch';
    case DISPATCHED = 'dispatched';
    case DELIVERED = 'delivered';

    public function getLabel(): string|Htmlable|null
    {
       return match ($this) {
           Self::FOR_REVIEW => 'FOR REVIEW',
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
}
