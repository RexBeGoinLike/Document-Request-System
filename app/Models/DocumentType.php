<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Relaticle\ActivityLog\Concerns\InteractsWithTimeline;
use Relaticle\ActivityLog\Contracts\HasTimeline;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class DocumentType extends Model implements HasTimeline
{


    use LogsActivity;
    use InteractsWithTimeline;
    public function requestQuantity(): HasMany {
        return $this->hasMany(RequestQuantity::class, 'document_type_id');
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
