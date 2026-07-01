<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
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

    use SoftDeletes;
    protected $primaryKey = 'request_id';

    protected $casts = [
        'for_mailing' => 'boolean',
    ];

    public function requestQuantity(): HasMany {
        return $this->hasMany(RequestQuantity::class, 'request_id', 'request_id');
    }

    public function deliveryDetail(): HasOne {
        return $this->hasOne(DeliveryDetail::class, 'request_id', 'request_id');
    }

    public function systemLog(): HasMany {
        return $this->hasMany(SystemLog::class, 'request_id', 'request_id');
    }
}
