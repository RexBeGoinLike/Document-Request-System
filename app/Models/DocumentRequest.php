<?php

namespace App\Models;

use App\DocumentRequestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Relaticle\ActivityLog\Concerns\InteractsWithTimeline;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'reference_id',
    'user_id',
    'requester_first_name',
    'requester_last_name',
    'requester_middle_name',
    'requester_suffix',
    'cadet_serial_number',
    'original_class',
    'contact_number',
    'street_address',
    'region',
    'city',
    'barangay',
    'zip_code',
    'self_collect',
    'is_mailing',
    'has_representative',
    'representative_first_name',
    'representative_last_name',
    'representative_middle_name',
    'representative_suffix',
    'representative_office',
    'representative_contact',
    'status',
])]
class DocumentRequest extends Model
{
    use SoftDeletes;
    use LogsActivity;
    use InteractsWithTimeline;

    protected $primaryKey = 'request_id';

    protected $casts = [
        'for_mailing' => 'boolean',
        'status'      =>  DocumentRequestStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }


    public function timeline(): TimelineBuilder
    {
        return TimelineBuilder::make($this)
            ->fromActivityLog()
            ->fromActivityLogOf(relations: ['requestQuantity', 'deliveryDetail']);
    }

    public function requestQuantity(): HasMany {
        return $this->hasMany(RequestQuantity::class, 'request_id', 'request_id');
    }

    public function processor(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function deliveryDetail(): HasOne {
        return $this->hasOne(DeliveryDetail::class, 'request_id', 'request_id');
    }
}
