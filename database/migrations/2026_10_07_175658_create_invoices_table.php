<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->restrictOnDelete();

            $table->date('invoice_date');

            $table->date('due_date')->nullable();

            $table->enum('status', [
                'draft',
                'issued',
                'cancelled',
            ])->default('draft');

            $table->decimal('subtotal', 12, 3)->default(0);
            $table->decimal('discount', 12, 3)->default(0);
            $table->decimal('tax', 12, 3)->default(0);
            $table->decimal('total', 12, 3)->default(0);

            $table->decimal('paid_amount', 12, 3)->default(0);
            $table->decimal('remaining_amount', 12, 3)->default(0);

            $table->enum('payment_status', [
                'unpaid',
                'partial',
                'paid',
                'overdue',
            ])->default('unpaid');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('invoice_date');
            $table->index('due_date');
            $table->index('payment_status');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};