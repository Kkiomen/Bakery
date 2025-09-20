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
        Schema::create('recipe_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');

            // Kolejność i typ kroku
            $table->integer('kolejnosc'); // kolejność wykonania
            $table->enum('typ', [
                'przygotowanie',    // przygotowanie składników
                'mieszanie',        // mieszanie składników
                'wyrabianie',       // wyrabianie ciasta
                'wyrastanie',       // pierwszy/drugi wzrost
                'formowanie',       // formowanie bułek/bochenków
                'odpoczynek',       // odpoczynek po formowaniu
                'wypiekanie',       // proces wypiekania
                'chłodzenie',       // chłodzenie po wypieku
                'dekorowanie',      // dekorowanie/glazurowanie
                'pakowanie'         // pakowanie gotowego produktu
            ]);

            // Opis kroku
            $table->string('nazwa'); // krótka nazwa kroku
            $table->text('opis'); // szczegółowy opis

            // Parametry czasowe
            $table->integer('czas_min')->nullable(); // czas trwania w minutach
            $table->integer('temperatura_c')->nullable(); // temperatura (dla wypiekania, wyrastania)
            $table->integer('wilgotnosc_proc')->nullable(); // wilgotność w % (dla wyrastania)

            // Parametry techniczne
            $table->string('narzedzia')->nullable(); // potrzebne narzędzia/sprzęt
            $table->text('wskazowki')->nullable(); // dodatkowe wskazówki
            $table->text('uwagi')->nullable(); // uwagi techniczne

            // Kontrola jakości
            $table->text('kryteria_oceny')->nullable(); // jak ocenić czy krok wykonany poprawnie
            $table->text('czeste_bledy')->nullable(); // częste błędy w tym kroku

            // Status
            $table->boolean('obowiazkowy')->default(true); // czy krok jest obowiązkowy
            $table->boolean('automatyczny')->default(false); // czy może być zautomatyzowany

            $table->timestamps();

            // Indeksy
            $table->index(['recipe_id', 'kolejnosc']);
            $table->index('typ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_steps');
    }
};
