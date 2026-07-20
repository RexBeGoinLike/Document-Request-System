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
            $table->id();
            $table->foreignId('request_id')
                ->constrained('document_requests')
                ->onUpdate('cascade');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'user_id')
                ->onUpdate('cascade');

            $table->string('first_name', 20);
            $table->string('last_name', 15);
            $table->string('middle_name', 15)->nullable();
            $table->string('suffix', 20)->nullable();
            $table->text('street_address');
            $table->string('region');
            $table->string('city');
            $table->string('barangay');
            $table->integer('zip_code');
            $table->string('contact_number', 11);

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
