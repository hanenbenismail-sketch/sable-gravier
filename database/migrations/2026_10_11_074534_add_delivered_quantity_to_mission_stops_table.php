<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mission_stops', function (Blueprint $table) {
            $table->decimal('delivered_quantity', 12, 3)
                ->default(0)
                ->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('mission_stops', function (Blueprint $table) {
            $table->dropColumn('delivered_quantity');
        });
    }
};
