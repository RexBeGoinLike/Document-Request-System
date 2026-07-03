<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Support\LogOptions;

class DocumentType extends Model
{

    protected $primaryKey = 'document_type_id';
    public function requestQuantity(): HasMany {
        return $this->hasMany(RequestQuantity::class, 'document_type_id', 'document_type_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function timeline(): TimelineBuilder
    {
        return TimelineBuilder::make($this)->fromActivityLog();
    }
}
