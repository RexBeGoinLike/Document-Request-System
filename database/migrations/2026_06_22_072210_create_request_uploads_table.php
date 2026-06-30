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
        Schema::create('request_uploads', function (Blueprint $table) {
            $table->id("request_upload_id");
            $table->foreignId('request_id')
                ->constrained('document_requests', 'request_id')
                ->onUpdate('cascade');
            $table->text('file_url')->nullable();
            $table->text('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('drive')->nullable();
            $table->string('path')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'user_id')
                ->onUpdate('cascade');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_uploads');
    }
};
