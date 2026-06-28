<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

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
class DocumentRequest extends Model
{
    use SoftDeletes;

    use SoftDeletes;
    protected $primaryKey = 'request_id';

    protected $casts = [
        'for_mailing' => 'boolean',
    ];

    public function requestQuantity(): HasMany {
        return $this->hasMany(RequestQuantity::class, 'request_id', 'request_id');
    }

    public function deliveryDetail(): HasOne {
        return $this->hasOne(DeliveryDetail::class, 'request_id', 'request_id');
    }

    public function systemLog(): HasMany {
        return $this->hasMany(SystemLog::class, 'request_id', 'request_id');
    }
}
