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
        Schema::create('delivery_details', function (Blueprint $table) {
            $table->id('delivery_detail_id');
            $table->foreignId('request_id')
                ->constrained('document_requests', 'request_id')
                ->onUpdate('cascade');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'user_id')
                ->onUpdate('cascade');

            $table->string('recipient_first_name', 20);
            $table->string('recipient_last_name', 15);
            $table->string('recipient_middle_name', 15)->nullable();
            $table->string('recipient_suffix', 20)->nullable();
            $table->text('street_address');
            $table->string('region');
            $table->string('city');
            $table->string('barangay');
            $table->integer('zip_code');
            $table->string('recipient_contact_number', 11);

            $table->string('tracking_number', 20)->nullable();
            $table->string('courier_name', 20)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_details');
    }
};
