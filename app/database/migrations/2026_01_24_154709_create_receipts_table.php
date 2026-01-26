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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('status')->default('uploaded');
            $table->longText('ocr_text_raw')->nullable();
            $table->longText('ocr_text_edited')->nullable();
            $table->decimal('parsed_total', 10, 2)->nullable();
            $table->decimal('confirmed_total', 10, 2)->nullable();
            $table->foreignId('store_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
