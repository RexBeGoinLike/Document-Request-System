<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Relaticle\ActivityLog\Concerns\InteractsWithTimeline;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'courier_name',
    'tracking_number',
    'request_id',
    'user_id',
    'for_mailing',
    'use_details',
    'first_name',
    'last_name',
    'middle_name',
    'suffix',
    'contact_number',
    'street_address',
    'region',
    'city',
    'barangay',
    'zip_code',
])]

class DeliveryDetail extends Model
{
    use LogsActivity;
    use InteractsWithTimeline;
    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class, 'request_id');
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
