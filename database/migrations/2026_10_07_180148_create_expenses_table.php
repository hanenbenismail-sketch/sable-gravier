<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->date('expense_date');

            $table->enum('category', [
                'fuel',
                'salary',
                'maintenance',
                'repair',
                'spare_parts',
                'insurance',
                'tax',
                'rent',
                'utilities',
                'other',
            ]);

            $table->string('description')->nullable();

            $table->decimal('amount', 12, 3);

            $table->foreignId('site_id')
                ->nullable()
                ->constrained('sites')
                ->nullOnDelete();

            $table->foreignId('truck_id')
                ->nullable()
                ->constrained('trucks')
                ->nullOnDelete();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('drivers')
                ->nullOnDelete();

            $table->foreignId('mission_id')
                ->nullable()
                ->constrained('missions')
                ->nullOnDelete();

            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'cheque',
                'card',
                'other',
            ])->default('cash');

            $table->string('reference')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('expense_date');
            $table->index('category');
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};