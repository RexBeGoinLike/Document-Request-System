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
        Schema::create('request_quantities', function (Blueprint $table) {
            $table->foreignId('request_id')
            ->constrained('document_requests', 'request_id')
            ->onUpdate('cascade');

            $table->foreignId('item_id')
            ->constrained('document_types', 'id')
            ->onUpdate('cascade');

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

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_quantities');
    }
};
