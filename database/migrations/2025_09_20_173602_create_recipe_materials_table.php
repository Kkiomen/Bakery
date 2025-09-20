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
        Schema::create('recipe_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');

            // Ilość składnika
            $table->decimal('ilosc', 10, 3); // ilość w jednostkach podstawowych materiału
            $table->string('jednostka'); // jednostka (powinna być zgodna z material.jednostka_podstawowa)

            // Dodatkowe informacje o składniku w recepturze
            $table->text('uwagi')->nullable(); // np. "przesiane", "w temperaturze pokojowej"
            $table->integer('kolejnosc')->nullable(); // kolejność dodawania składnika
            $table->boolean('opcjonalny')->default(false); // czy składnik jest opcjonalny

            // Przetwarzanie składnika
            $table->string('sposob_przygotowania')->nullable(); // np. "roztopione", "ubite na pianę"
            $table->integer('temperatura_c')->nullable(); // temperatura składnika przy dodawaniu

            $table->timestamps();

            // Indeksy
            $table->unique(['recipe_id', 'material_id']);
            $table->index(['recipe_id', 'kolejnosc']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_materials');
    }
};
