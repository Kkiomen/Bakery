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
        Schema::create('delivery_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size');
            $table->string('mime_type', 100);
            $table->text('opis')->nullable();
            $table->enum('typ_zdjecia', ['produkty', 'dowod_dostawy', 'problem', 'lokalizacja', 'inne'])->default('produkty');
            $table->integer('kolejnosc')->default(0);

            // Lokalizacja GPS gdzie zrobiono zdjęcie
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->datetime('data_wykonania')->nullable();

            $table->timestamps();

            // Indeksy
            $table->index(['delivery_id', 'typ_zdjecia']);
            $table->index('kolejnosc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_photos');
    }
};
