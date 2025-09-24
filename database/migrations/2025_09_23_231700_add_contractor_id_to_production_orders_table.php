<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            // Dodaj pole contractor_id jeśli nie istnieje
            if (!Schema::hasColumn('production_orders', 'contractor_id')) {
                $table->unsignedBigInteger('contractor_id')->nullable()->after('klient');
                $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            // Usuń pole contractor_id
            if (Schema::hasColumn('production_orders', 'contractor_id')) {
                $table->dropForeign(['contractor_id']);
                $table->dropColumn('contractor_id');
            }
        });
    }
};
