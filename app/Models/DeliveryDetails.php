<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'request_id', 'user_id', 'delivery_detail_id'
])]
class DeliveryDetails extends Model
{
    //
}
