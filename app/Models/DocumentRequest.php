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
use Yajra\Address\Entities\Barangay;

#[Fillable([
    'reference_id',
    'user_id',
    'first_name',
    'last_name',
    'middle_name',
    'suffix',
    'serial_number',
    'class',
    'contact_number',
    'street_address',
    'region',
    'city',
    'amount_payable',
    'barangay',
    'zip_code',
    'has_representative',
    'is_mailing',
    'status',
])]
class DocumentRequest extends Model
{
    use SoftDeletes;
    use LogsActivity;
    use InteractsWithTimeline;


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
        return $this->hasMany(RequestQuantity::class, 'request_id');
    }

    public function processor(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deliveryDetail(): HasOne {
        return $this->hasOne(DeliveryDetail::class, 'request_id');
    }

    public function representative(): HasOne {
        return $this->hasOne(Representative::class, 'request_id');
    }
}
