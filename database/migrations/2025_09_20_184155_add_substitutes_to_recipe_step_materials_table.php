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
        Schema::table('recipe_step_materials', function (Blueprint $table) {
            // JSON z zamiennikami - tablica obiektów z material_id, współczynnik_przeliczenia, uwagi
            $table->json('substitutes')->nullable()->after('temperature_c');
            // Czy składnik ma aktywne zamienniki
            $table->boolean('has_substitutes')->default(false)->after('substitutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_step_materials', function (Blueprint $table) {
            $table->dropColumn(['substitutes', 'has_substitutes']);
        });
    }
};
