<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('budget_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('raw_name')->nullable();
            $table->decimal('quantity', 11, 5);
            $table->decimal('unit_price', 10);
            $table->decimal('total_price', 10);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_items');
    }
};
