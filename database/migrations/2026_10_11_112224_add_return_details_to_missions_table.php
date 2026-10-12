
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->decimal('returned_quantity', 12, 3)
                ->default(0)
                ->after('return_time');

            $table->foreignId('return_site_id')
                ->nullable()
                ->after('departure_site_id')
                ->constrained('sites')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('return_site_id');
            $table->dropColumn('returned_quantity');
        });
    }
};
