<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'diploma_total_price',
    'transcript_total_price',
    'cert_cadet_total_price',
    'cert_graduate_total_price',
    'cert_authenticity_total_price',
    'cert_course_desc_total_price',
    'cert_latin_honors_total_price',
    'cert_good_moral_total_price',
    'cert_ranking_gpa_total_price',
    'cert_honorable_dismissal_total_price',
    'cert_instruction_medium_total_price',
    'cert_transfer_credentials_total_price',
    'cert_service_record_total_price',
    'photocopy_total_price',
    'mailing_price',
    'others'
])]

class requestFees extends Model
{
    //
}
