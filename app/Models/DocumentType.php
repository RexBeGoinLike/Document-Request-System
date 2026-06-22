<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentType extends Model
{
    public function requestQuantity(): BelongsTo {
        return $this->belongsTo(RequestQuantity::class, 'document_type_id', 'document_type_id');
    }
}
