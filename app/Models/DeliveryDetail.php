<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'request_id',
    'user_id',
    'for_mailing',
    'use_details',
    'recipient_first_name',
    'recipient_last_name',
    'recipient_middle_name',
    'recipient_suffix',
    'recipient_contact_number',
    'street_address',
    'region',
    'city',
    'barangay',
    'zip_code',
])]

class DeliveryDetail extends Model
{
    protected $primaryKey = 'delivery_detail_id';
    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class, 'request_id', 'request_id');
    }
}
