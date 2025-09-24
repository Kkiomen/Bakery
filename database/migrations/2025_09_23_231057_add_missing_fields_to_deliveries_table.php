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
        Schema::table('deliveries', function (Blueprint $table) {
            // Dodaj pole contractor_id jeśli nie istnieje
            if (!Schema::hasColumn('deliveries', 'contractor_id')) {
                $table->unsignedBigInteger('contractor_id')->nullable()->after('production_order_id');
                $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('set null');
            }

            // Dodaj pola godzin jeśli nie istnieją
            if (!Schema::hasColumn('deliveries', 'godzina_od')) {
                $table->time('godzina_od')->nullable()->after('data_dostawy');
            }
            if (!Schema::hasColumn('deliveries', 'godzina_do')) {
                $table->time('godzina_do')->nullable()->after('godzina_od');
            }

            // Dodaj pole adres_dostawy jeśli nie istnieje
            if (!Schema::hasColumn('deliveries', 'adres_dostawy')) {
                $table->text('adres_dostawy')->nullable()->after('klient_adres');
            }

            // Dodaj pole uwagi jeśli nie istnieje (krótsze od uwagi_dostawy)
            if (!Schema::hasColumn('deliveries', 'uwagi')) {
                $table->text('uwagi')->nullable()->after('miasto');
            }

            // Dodaj pole typ_dostawy jeśli nie istnieje
            if (!Schema::hasColumn('deliveries', 'typ_dostawy')) {
                $table->string('typ_dostawy')->default('standardowa')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Usuń dodane pola
            if (Schema::hasColumn('deliveries', 'contractor_id')) {
                $table->dropForeign(['contractor_id']);
                $table->dropColumn('contractor_id');
            }
            if (Schema::hasColumn('deliveries', 'godzina_od')) {
                $table->dropColumn('godzina_od');
            }
            if (Schema::hasColumn('deliveries', 'godzina_do')) {
                $table->dropColumn('godzina_do');
            }
            if (Schema::hasColumn('deliveries', 'adres_dostawy')) {
                $table->dropColumn('adres_dostawy');
            }
            if (Schema::hasColumn('deliveries', 'uwagi')) {
                $table->dropColumn('uwagi');
            }
            if (Schema::hasColumn('deliveries', 'typ_dostawy')) {
                $table->dropColumn('typ_dostawy');
            }
        });
    }
};
