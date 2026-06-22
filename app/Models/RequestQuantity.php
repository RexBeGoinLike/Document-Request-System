<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestQuantity extends Model
{
    protected $table = 'request_quantities';

    public function documentRequests(): BelongsToMany {
        return $this->belongsToMany(DocumentRequest::class, 'request_id', 'request_id');
    }

    public Function documentType(): HasOne {
        return $this->hasOne(DocumentType::class, 'document_type_id', 'document_type_id');
    }
}
