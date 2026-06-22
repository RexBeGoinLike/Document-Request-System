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
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'user_id')
                ->onUpdate('cascade');

            $table->string('status')->default('pending');
            $table->string('requester_first_name');
            $table->string('requester_last_name');
            $table->string('requester_middle_name')->nullable();
            $table->string('requester_suffix')->nullable();
            $table->string('cadet_serial_number');
            $table->string('original_class');
            $table->integer('contact_number');
            $table->string('street_address');
            $table->string('region');
            $table->string('city');
            $table->string('barangay');
            $table->integer('zip_code');
            $table->boolean('for_mailing');
            $table->text('others')->nullable();
            $table->string('representative_name')->nullable();
            $table->string('representative_office')->nullable();
            $table->integer('representative_contact')->nullable();
            $table->string('signature_image');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
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
