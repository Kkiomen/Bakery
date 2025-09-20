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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();

            // Podstawowe dane surowca
            $table->string('kod')->unique(); // np. MAK-001, CUK-001
            $table->string('nazwa');
            $table->text('opis')->nullable();
            $table->string('typ'); // mąka, cukier, drożdże, tłuszcze, dodatki, etc.

            // Jednostki i opakowania
            $table->string('jednostka_podstawowa'); // kg, l, szt, etc.
            $table->decimal('waga_opakowania', 8, 3)->nullable(); // waga opakowania w kg
            $table->string('dostawca')->nullable();

            // Magazyn i stany
            $table->decimal('stan_aktualny', 10, 3)->default(0); // aktualny stan w jednostkach podstawowych
            $table->decimal('stan_minimalny', 10, 3)->default(0); // próg ostrzeżenia
            $table->decimal('stan_optymalny', 10, 3)->default(0); // optymalny stan magazynowy

            // Ceny
            $table->integer('cena_zakupu_gr')->nullable(); // cena zakupu za jednostkę w groszach
            $table->enum('stawka_vat', ['0', '5', '8', '23'])->default('23');

            // Daty ważności i jakość
            $table->integer('dni_waznosci')->nullable(); // ile dni surowiec jest ważny
            $table->date('data_ostatniej_dostawy')->nullable();
            $table->text('uwagi')->nullable();

            // Status
            $table->boolean('aktywny')->default(true);

            $table->timestamps();

            // Indeksy
            $table->index(['typ', 'aktywny']);
            $table->index('stan_aktualny');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
