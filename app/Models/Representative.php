<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Relaticle\ActivityLog\Concerns\InteractsWithTimeline;
use Relaticle\ActivityLog\Contracts\HasTimeline;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'request_id',
    'first_name',
    'last_name',
    'middle_name',
    'suffix',
    'office',
    'contact',
    'relationship',
    'authorization_letter'
])]

class Representative extends Model implements HasTimeline
{

    use LogsActivity;
    use InteractsWithTimeline;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function documentRequest() : BelongsTo {
        return $this->belongsTo(DocumentRequest::class, 'request_id');
    }

    public function timeline(): TimelineBuilder
    {
        return TimelineBuilder::make($this)->fromActivityLog();
    }
}
