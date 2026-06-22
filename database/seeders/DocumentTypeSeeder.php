<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $document_types = [
            ['document_type' => 'Diploma', 'document_fee' => 350, 'document_classification' => 'others'],
            ['document_type' => 'Transcript of Records', 'document_fee' => 50, 'document_classification' => 'others'],
            ['document_type' => 'As Cadet', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'As Graduate', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'Cert and Authenticity Verification', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'Course Description/Page', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'Cum Laude (Latin Awards)', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'Good Moral', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'GPA/Ranking', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'Honorable Dismissal', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'Medium of Instruction', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'Transfer Credentials', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'Service Records/Statement of Service', 'document_fee' => 50, 'document_classification' => 'certification'],
            ['document_type' => 'Photocopying & Authentication', 'document_fee' => 50, 'document_classification' => 'others'],
            ['document_type' => 'Mailing', 'document_fee' => 200, 'document_classification' => 'mailing']
        ];

        DocumentType::upsert($document_types, ['document_type'], ['document_fee', 'document_classification']);
    }
}
