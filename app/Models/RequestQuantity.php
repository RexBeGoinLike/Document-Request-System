<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Relaticle\ActivityLog\Concerns\InteractsWithTimeline;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['document_type_id',
    'fee',
    'quantity',
    'total_fee',])]
class RequestQuantity extends Model
{
    use LogsActivity;
    use InteractsWithTimeline;

    protected $table = 'request_quantities';

    protected $primaryKey = 'request_quantity_id';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function timeline(): TimelineBuilder
    {
        return TimelineBuilder::make($this)->fromActivityLog();
    }

    public function documentRequestsss(): BelongsToMany {
        return $this->belongsToMany(DocumentRequest::class, 'request_id', 'request_id');
    }

    public Function documentType(): BelongsTo {
        return $this->BelongsTo(DocumentType::class, 'document_type_id', 'document_type_id');
    }
}
