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
            $table->id();

            $table->string('reference_id', 11)->unique();

            $table->foreignId('last_field_modified_by')
                ->nullable()
                ->constrained('users', 'user_id')
                ->onUpdate('cascade');

            $table->foreignId('last_status_modified_by')
                ->nullable()
                ->constrained('users', 'user_id')
                ->onUpdate('cascade');

            $table->string('status')->default(DocumentRequestStatus::FOR_REVIEW->value);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('suffix', 10)->nullable();
            $table->string('serial_number', 6);
            $table->string('class', 4);
            $table->string('contact_number', 11);
            $table->text('street_address');
            $table->string('region');
            $table->string('city');
            $table->string('barangay');
            $table->string('zip_code', 4);
            $table->decimal('amount_payable');
            $table->boolean('is_mailing')->default(false);
            $table->boolean('has_representative')->default(false);
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
