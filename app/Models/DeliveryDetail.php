<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'request_id', 'user_id', 'delivery_detail_id'
])]
class DeliveryDetail extends Model
{
    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class, 'request_id', 'request_id');
    }
}
