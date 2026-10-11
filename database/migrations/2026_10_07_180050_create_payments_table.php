<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->restrictOnDelete();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->restrictOnDelete();

            $table->date('payment_date');

            $table->decimal('amount', 12, 3);

            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'cheque',
                'card',
                'other',
            ])->default('cash');

            $table->string('reference')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('payment_date');
            $table->index('payment_method');
            $table->index('invoice_id');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};