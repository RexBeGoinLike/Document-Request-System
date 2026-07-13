<?php

use App\DocumentRequestStatus;
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

            $table->string('reference_id', 11)->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'user_id')
                ->onUpdate('cascade');

            $table->string('status')->default(DocumentRequestStatus::FOR_REVIEW->value);
            $table->string('requester_first_name', 20);
            $table->string('requester_last_name', 15);
            $table->string('requester_middle_name', 15);
            $table->string('requester_suffix', 20)->nullable();
            $table->string('cadet_serial_number', 4);
            $table->string('original_class', 20);
            $table->string('contact_number', 11);
            $table->text('street_address');
            $table->string('region');
            $table->string('city');
            $table->string('barangay');
            $table->tinyInteger('zip_code');
            $table->boolean('is_mailing')->default(false);
            $table->boolean('has_representative')->default(false);
            $table->string('representative_first_name', 20)->nullable();
            $table->string('representative_last_name', 15)->nullable();
            $table->string('representative_middle_name', 15)->nullable();
            $table->string('representative_suffix', 20)->nullable();
            $table->string('representative_office', 20)->nullable();
            $table->string('representative_contact', 11)->nullable();
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
