<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['document_type_id',
    'fee',
    'quantity',
    'total_fee',])]
class RequestQuantity extends Model
{
    protected $table = 'request_quantities';

    protected $primaryKey = 'request_quantity_id';

    public function documentRequests(): BelongsToMany {
        return $this->belongsToMany(DocumentRequest::class, 'request_id', 'request_id');
    }

    public Function documentType(): BelongsToMany {
        return $this->BelongsToMany(DocumentType::class, 'document_type_id', 'document_type_id');
    }
}
