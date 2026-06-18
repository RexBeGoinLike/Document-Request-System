<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\DocumentRequestStatus;
use Illuminate\Validation\Rules\Enum;

class DocumentReqRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //Documents to be requested
        $countFields = [
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
        ];

        $dynamicRules = [];

        //Require at least one selected document count field to be filled
        foreach ($countFields as $field) {
            $otherFields = array_diff($countFields, [$field]);
            $otherFieldsString = implode(',', $otherFields);

            $dynamicRules[$field] = [
                "required_without_all:{$otherFieldsString}",
                'nullable',
                'integer',
                'min:0',
                'gt:0'
            ];
        }

        $standardRules = [
          'requester_name' => ['required', 'string'],
          'cadet_serial_number' => ['required', 'string'],
          'original_class' => ['required', 'string'],
          'contact_number' => ['required', 'integer'],
          'address' => ['required', 'string'],
          'for_mailing' => ['required', 'boolean'],
          'others' => ['string'],
          'status' => ['required', 'string', new Enum(DocumentRequestStatus::class)],
          'representative_name' => ['string'],
          'representative_contact' => ['string'],
          'signature_image' => ['required', 'string']
        ];

        //Merge the standard rules with the dynamic rules
        return array_merge($standardRules, $dynamicRules);
    }

    public function messages(): array
    {
        $fields = array_keys($this->rules());
        $ruleTemplates = [
            'required'             => 'The :attribute field cannot be left blank.',
            'integer'              => 'The :attribute must be a valid whole number.',
            'string'               => 'The :attribute must be valid text.',
            'max'                  => 'The :attribute is too long.',
            'gt'                   => 'The :attribute amount must be greater than zero.',
            'required_without_all' => 'You must select or request at least one document copy to proceed.',
        ];

        $automatedMessages = [];

        //Spoopy
        foreach ($fields as $field) {
            foreach ($ruleTemplates as $rule => $messageTemplate) {
                $automatedMessages["{$field}.{$rule}"] = $messageTemplate;
            }
        }

        return $automatedMessages;
    }

    public function attributes(): array{
        $fields = array_keys($this->rules());
        $customAttributes = [];

        foreach ($fields as $field) {
            // Remove '_count' from SQL names so it sounds natural
            $cleanName = str_replace('_count', '', $field);

            $cleanName = str_replace('_', ' ', $cleanName);

            $cleanName = str_replace('cert', 'Certification', $cleanName);

            $customAttributes[$field] = ucwords($cleanName);
        }

        $customAttributes['cert_cadet_count'] = 'Certification As Cadet';
        $customAttributes['cert_graduate_count'] = 'Certification As Graduate';
        $customAttributes['cert_authenticity_count'] = 'Certification and Authenticity Verification';
        $customAttributes['cert_course_desc_count'] = 'Course Description Certification';
        $customAttributes['cert_latin_honors_count'] = 'Cum Laude (Latin Honors) Certification';
        $customAttributes['cert_ranking_gpa_count'] = 'Ranking/GPA Certification';
        $customAttributes['cert_instruction_medium_count'] = 'Medium of Instruction Certification';
        $customAttributes['cert_service_record_count'] = 'Service Record/Statement of Service Certification';
        $customAttributes['photocopy_count'] = 'Photocopying and Authentication';
        $customAttributes['signature_image'] = 'Signature';

        return $customAttributes;
    }
}
