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
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->string('requester_name');
            $table->string('cadet_serial_number');
            $table->string('original_class');
            $table->integer('contact_number');
            $table->string('address');
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
