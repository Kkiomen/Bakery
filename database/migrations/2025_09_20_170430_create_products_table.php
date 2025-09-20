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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Podstawowe dane produktu
            $table->string('sku')->unique();
            $table->string('ean', 14)->nullable()->unique();
            $table->string('nazwa');
            $table->text('opis')->nullable();

            // Relacja do kategorii
            $table->foreignId('kategoria_id')->constrained('categories')->onDelete('cascade');

            // Waga i jednostki
            $table->integer('waga_g'); // waga w gramach
            $table->enum('jednostka_sprzedazy', ['szt', 'opak', 'kg'])->default('szt');
            $table->integer('zawartosc_opakowania')->nullable();

            // Alergeny i wartości odżywcze (JSON)
            $table->json('alergeny')->nullable();
            $table->json('wartosci_odzywcze')->nullable();

            // Ceny i VAT
            $table->enum('stawka_vat', ['0', '5', '8', '23']);
            $table->integer('cena_netto_gr'); // cena w groszach

            // Status
            $table->boolean('aktywny')->default(true);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();

            // Indeksy
            $table->index(['aktywny', 'kategoria_id']);
            $table->index('nazwa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
