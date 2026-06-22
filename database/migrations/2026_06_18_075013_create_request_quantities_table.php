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

            $table->decimal('fee', 10, 2)->default(0);

            $table->decimal('total_fee', 10, 2)->default(0);
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
