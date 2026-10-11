<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();

            $table->string('name');

            $table->string('company_name')->nullable();

            $table->string('phone')->nullable();
            $table->string('phone_secondary')->nullable();

            $table->string('email')->nullable();

            $table->string('address')->nullable();

            $table->string('gouvernorat')->nullable();
            $table->string('delegation')->nullable();

            $table->string('tax_number')->nullable();

            $table->enum('client_type', [
                'individual',
                'company',
            ])->default('company');

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->decimal('credit_limit', 12, 3)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('phone');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};