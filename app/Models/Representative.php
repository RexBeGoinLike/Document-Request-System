<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'request_id',
    'first_name',
    'last_name',
    'middle_name',
    'suffix',
    'office',
    'contact',
    'relationship',
    'authorization_letter'
])]
class Representative extends Model
{
    public function documentRequest() : BelongsTo {
        return $this->belongsTo(DocumentRequest::class, 'request_id');
    }
}
