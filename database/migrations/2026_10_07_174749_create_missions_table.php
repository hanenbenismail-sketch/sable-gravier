<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();

            $table->string('reference')->unique();

            $table->foreignId('truck_id')
                ->constrained('trucks')
                ->restrictOnDelete();

            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->restrictOnDelete();

            $table->foreignId('departure_site_id')
                ->constrained('sites')
                ->restrictOnDelete();

            $table->date('mission_date');

            $table->time('departure_time')->nullable();
            $table->time('return_time')->nullable();

            $table->enum('mission_type', [
                'delivery',
                'transfer',
                'return',
                'mixed',
            ])->default('delivery');

            $table->enum('status', [
                'planned',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('planned');

            $table->decimal('total_distance', 12, 3)->default(0);
            $table->decimal('total_quantity', 12, 3)->default(0);

            $table->decimal('transport_cost', 12, 3)->default(0);
            $table->decimal('fuel_cost', 12, 3)->default(0);
            $table->decimal('driver_cost', 12, 3)->default(0);
            $table->decimal('other_cost', 12, 3)->default(0);

            $table->decimal('total_cost', 12, 3)->default(0);
            $table->decimal('total_revenue', 12, 3)->default(0);
            $table->decimal('profit', 12, 3)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('mission_date');
            $table->index('status');
            $table->index('mission_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};