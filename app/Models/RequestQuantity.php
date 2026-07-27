<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Relaticle\ActivityLog\Concerns\InteractsWithTimeline;
use Relaticle\ActivityLog\Contracts\HasTimeline;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['document_type_id',
    'fee',
    'quantity',
    'total_fee',])]
class RequestQuantity extends Model implements HasTimeline
{
    use LogsActivity;
    use InteractsWithTimeline;

    protected $table = 'request_quantities';


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function timeline(): TimelineBuilder
    {
        return TimelineBuilder::make($this)->fromActivityLog();
    }

    public function documentRequest(): BelongsTo {
        return $this->belongsTo(DocumentRequest::class, 'request_id');
    }

    public Function documentType(): BelongsTo {
        return $this->BelongsTo(DocumentType::class, 'document_type_id');
    }
}
