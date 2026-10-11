<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');

            $table->string('phone')->nullable();

            $table->string('license_number')->unique();
            $table->string('license_category')->nullable();

            $table->enum('salary_type', [
                'monthly',
                'daily',
                'per_trip',
                'per_km',
            ])->default('monthly');

            $table->decimal('base_salary', 12, 3)->default(0);
            $table->decimal('salary_per_day', 12, 3)->default(0);
            $table->decimal('salary_per_trip', 12, 3)->default(0);
            $table->decimal('salary_per_km', 12, 3)->default(0);
            $table->decimal('bonus', 12, 3)->default(0);

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};