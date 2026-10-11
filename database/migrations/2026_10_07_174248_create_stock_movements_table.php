<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->cascadeOnDelete();

            $table->enum('type', [
                'IN',
                'OUT',
                'TRANSFER',
                'ADJUSTMENT',
                'RETURN',
            ]);

            $table->decimal('quantity', 12, 3);

            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('from_site_id')
                ->nullable()
                ->constrained('sites')
                ->nullOnDelete();

            $table->foreignId('to_site_id')
                ->nullable()
                ->constrained('sites')
                ->nullOnDelete();

            $table->dateTime('movement_date');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index('movement_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};