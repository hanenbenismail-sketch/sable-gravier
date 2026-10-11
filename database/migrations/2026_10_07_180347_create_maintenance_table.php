<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance', function (Blueprint $table) {
            $table->id();

            $table->foreignId('truck_id')
                ->constrained('trucks')
                ->restrictOnDelete();

            $table->date('maintenance_date');

            $table->enum('type', [
                'oil_change',
                'service',
                'repair',
                'tires',
                'brakes',
                'engine',
                'electrical',
                'technical_inspection',
                'other',
            ]);

            $table->string('description')->nullable();

            $table->unsignedBigInteger('current_kilometer')->default(0);

            $table->string('garage')->nullable();

            $table->decimal('cost', 12, 3)->default(0);

            $table->date('next_maintenance_date')->nullable();

            $table->unsignedBigInteger('next_maintenance_kilometer')->nullable();

            $table->enum('status', [
                'planned',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('completed');

            $table->string('reference')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('maintenance_date');
            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance');
    }
};