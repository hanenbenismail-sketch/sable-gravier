<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('truck_id')
                ->constrained('trucks')
                ->restrictOnDelete();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('drivers')
                ->nullOnDelete();

            $table->foreignId('mission_id')
                ->nullable()
                ->constrained('missions')
                ->nullOnDelete();

            $table->date('fuel_date');

            $table->unsignedBigInteger('kilometer');

            $table->decimal('liters', 12, 3);

            $table->decimal('price_per_liter', 12, 3);

            $table->decimal('total_amount', 12, 3);

            $table->string('fuel_station')->nullable();

            $table->string('reference')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('fuel_date');
            $table->index('truck_id');
            $table->index('mission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_records');
    }
};