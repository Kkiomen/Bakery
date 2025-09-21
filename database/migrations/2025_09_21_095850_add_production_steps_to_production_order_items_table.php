<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_order_items', function (Blueprint $table) {
            $table->enum('current_step', [
                'waiting',           // Oczekuje
                'preparing',         // Przygotowanie składników
                'mixing',           // Mieszanie/Zagniatanie
                'first_rise',       // Pierwszy wyrośnięcie
                'shaping',          // Formowanie
                'second_rise',      // Drugi wyrośnięcie
                'baking',           // Pieczenie
                'cooling',          // Studzenie
                'packaging',        // Pakowanie
                'completed'         // Ukończone
            ])->default('waiting')->after('status');

            $table->timestamp('step_started_at')->nullable()->after('current_step');
            $table->json('step_notes')->nullable()->after('step_started_at'); // Notatki do każdego kroku
        });
    }

    public function down(): void
    {
        Schema::table('production_order_items', function (Blueprint $table) {
            $table->dropColumn(['current_step', 'step_started_at', 'step_notes']);
        });
    }
};
