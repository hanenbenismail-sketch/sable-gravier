<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->string('description')->nullable();

            $table->decimal('quantity', 12, 3);

            $table->string('unit')->default('tonne');

            $table->decimal('unit_price', 12, 3);

            $table->decimal('discount', 12, 3)->default(0);

            $table->decimal('tax_rate', 5, 2)->default(0);

            $table->decimal('subtotal', 12, 3)->default(0);

            $table->decimal('total', 12, 3)->default(0);

            $table->foreignId('mission_id')
                ->nullable()
                ->constrained('missions')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('invoice_id');
            $table->index('product_id');
            $table->index('mission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};