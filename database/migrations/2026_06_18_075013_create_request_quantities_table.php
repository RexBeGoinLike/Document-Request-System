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
            $table->id("request_quantity_id");

            $table->foreignId('request_id')
            ->constrained('document_requests', 'request_id')
            ->onUpdate('cascade');

            $table->foreignId('document_type_id')
            ->constrained('document_types', 'document_type_id')
            ->onUpdate('cascade');

            $table->integer('quantity')->default(0);

            $table->double('fee', 10, 2)->default(0);

            $table->double('total_fee', 10, 2)->default(0);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

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
