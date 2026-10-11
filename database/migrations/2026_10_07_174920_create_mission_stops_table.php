<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_stops', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mission_id')
                ->constrained('missions')
                ->cascadeOnDelete();

            $table->unsignedInteger('stop_order');

            $table->enum('stop_type', [
                'loading',
                'delivery',
                'transfer',
                'return',
                'unloading',
            ]);

            $table->foreignId('site_id')
                ->nullable()
                ->constrained('sites')
                ->nullOnDelete();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->decimal('quantity', 12, 3)->default(0);

            $table->string('unit')->default('tonne');

            $table->decimal('distance_from_previous', 12, 3)->default(0);

            $table->enum('loading_status', [
                'pending',
                'loaded',
                'not_applicable',
            ])->default('pending');

            $table->enum('delivery_status', [
                'pending',
                'delivered',
                'partial',
                'not_applicable',
            ])->default('pending');

            $table->dateTime('arrival_time')->nullable();
            $table->dateTime('departure_time')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['mission_id', 'stop_order']);
            $table->index('stop_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_stops');
    }
};