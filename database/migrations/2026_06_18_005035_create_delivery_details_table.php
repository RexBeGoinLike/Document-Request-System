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

            $table->string('recipient_name');
            $table->text('recipient_address');
            $table->integer('recipient_contact_number');

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
