<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('document_types')->insertOrIgnore([
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
            ['document_type' => 'Mailing', 'document_fee' => 200, 'document_classification' => 'mailing'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
