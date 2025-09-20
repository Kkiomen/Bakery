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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();

            // Podstawowe dane receptury
            $table->string('kod')->unique(); // np. REC-BULKA-001
            $table->string('nazwa');
            $table->text('opis')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null'); // do jakiego produktu należy

            // Wydajność
            $table->integer('ilosc_porcji')->default(1); // ile sztuk/porcji z receptury
            $table->decimal('waga_jednostkowa_g', 8, 2)->nullable(); // waga jednej sztuki w gramach

            // Czasy
            $table->integer('czas_przygotowania_min')->nullable(); // czas przygotowania w minutach
            $table->integer('czas_wypiekania_min')->nullable(); // czas wypiekania
            $table->integer('czas_calkowity_min')->nullable(); // całkowity czas (z wyrastaniem, etc.)

            // Parametry wypiekania
            $table->integer('temperatura_c')->nullable(); // temperatura pieca w Celsjuszach
            $table->text('instrukcje_wypiekania')->nullable(); // dodatkowe instrukcje

            // Poziom trudności i kategoria
            $table->enum('poziom_trudnosci', ['łatwy', 'średni', 'trudny'])->default('średni');
            $table->string('kategoria')->nullable(); // chleby, bułki, ciasta, etc.

            // Uwagi i notatki
            $table->text('uwagi')->nullable();
            $table->text('wskazowki')->nullable(); // wskazówki technologiczne

            // Status
            $table->boolean('aktywny')->default(true);
            $table->boolean('testowany')->default(false); // czy receptura została przetestowana

            // Autor i wersja
            $table->string('autor')->nullable();
            $table->string('wersja', 10)->default('1.0');

            $table->timestamps();

            // Indeksy
            $table->index(['kategoria', 'aktywny']);
            $table->index('poziom_trudnosci');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
