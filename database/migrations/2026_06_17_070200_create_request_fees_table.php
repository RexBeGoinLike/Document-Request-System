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
        Schema::create('request_fees', function (Blueprint $table) {
            $table->foreignId('request_id')
                ->constrained('requests', 'request_id');

            $table->integer('diploma_total_price')->nullable();
            $table->integer('transcript_total_price')->nullable();
            $table->integer('cert_cadet_total_price')->nullable();
            $table->integer('cert_graduate_total_price')->nullable();
            $table->integer('cert_authenticity_total_price')->nullable();
            $table->integer('cert_course_desc_total_price')->nullable();
            $table->integer('cert_latin_honors_total_price')->nullable();
            $table->integer('cert_good_moral_total_price')->nullable();
            $table->integer('cert_ranking_gpa_total_price')->nullable();
            $table->integer('cert_honorable_dismissal_total_price')->nullable();
            $table->integer('cert_instruction_medium_total_price')->nullable();
            $table->integer('cert_transfer_credentials_total_price')->nullable();
            $table->integer('cert_service_record_total_price')->nullable();
            $table->integer('photocopy_total_price')->nullable();
            $table->boolean('mailing_price');
            $table->text('others')->nullable();
            $table->timestamps();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_fees');
    }
};
