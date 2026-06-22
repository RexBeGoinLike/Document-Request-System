<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([])]
class SystemLog extends Model
{
    public function documentRequest(): BelongsToMany
    {
        return $this->belongsToMany(DocumentRequest::class, 'request_id', 'request_id');
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_id', 'user_id');
    }
}
