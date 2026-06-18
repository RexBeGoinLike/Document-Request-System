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
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->string('status');
            $table->timestamps();
            $table->string('requester_name');
            $table->string('cadet_serial_number');
            $table->string('original_class');
            $table->integer('contact_number');
            $table->string('address');
            $table->integer('diploma_count')->nullable();
            $table->integer('transcript_count')->nullable();
            $table->integer('cert_cadet_count')->nullable();
            $table->integer('cert_graduate_count')->nullable();
            $table->integer('cert_authenticity_count')->nullable();
            $table->integer('cert_course_desc_count')->nullable();
            $table->integer('cert_latin_honors_count')->nullable();
            $table->integer('cert_good_moral_count')->nullable();
            $table->integer('cert_ranking_gpa_count')->nullable();
            $table->integer('cert_honorable_dismissal_count')->nullable();
            $table->integer('cert_instruction_medium_count')->nullable();
            $table->integer('cert_transfer_credentials_count')->nullable();
            $table->integer('cert_service_record_count')->nullable();
            $table->integer('photocopy_count')->nullable();
            $table->boolean('for_mailing');
            $table->text('others')->nullable();
            $table->string('representative_name')->nullable();
            $table->string('representative_office')->nullable();
            $table->integer('representative_contact')->nullable();
            $table->string('signature_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
