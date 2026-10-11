<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();

            $table->string('registration_number')->unique();

            $table->string('brand')->nullable();
            $table->string('model')->nullable();

            $table->decimal('capacity', 12, 3);
            $table->string('unit')->default('tonne');

            $table->unsignedBigInteger('current_kilometer')->default(0);

            $table->decimal('fuel_consumption', 8, 2)->nullable();

            $table->enum('status', [
                'AVAILABLE',
                'IN_MISSION',
                'MAINTENANCE',
                'INACTIVE',
            ])->default('AVAILABLE');

            $table->date('insurance_expiry')->nullable();
            $table->date('technical_visit_expiry')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};