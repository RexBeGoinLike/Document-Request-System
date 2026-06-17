<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['requester_name',
    'cadet_serial_number',
    'original_class',
    'contact_number',
    'address',
    'diploma_count',
    'transcript_count',
    'cert_cadet_count',
    'cert_graduate_count',
    'cert_authenticity_count',
    'cert_course_desc_count',
    'cert_latin_honors_count',
    'cert_good_moral_count',
    'cert_ranking_gpa_count',
    'cert_honorable_dismissal_count',
    'cert_instruction_medium_count',
    'cert_transfer_credentials_count',
    'cert_service_record_count',
    'photocopy_count',
    'for_mailing',
    'representative_name',
    'representative_office',
    'representative_contact',
    'signature_image'
    ])]

#[Hidden(['password', 'remember_token'])]
class Request extends Model
{
}
