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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('numer_dostawy')->unique();
            $table->foreignId('production_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['oczekujaca', 'przypisana', 'w_drodze', 'dostarczona', 'anulowana', 'problem'])->default('oczekujaca');
            $table->enum('priorytet', ['niski', 'normalny', 'wysoki', 'pilny'])->default('normalny');
            $table->date('data_dostawy');
            $table->datetime('godzina_planowana')->nullable();
            $table->datetime('godzina_rozpoczecia')->nullable();
            $table->datetime('godzina_zakonczenia')->nullable();

            // Dane klienta
            $table->string('klient_nazwa');
            $table->text('klient_adres');
            $table->string('klient_telefon')->nullable();
            $table->string('klient_email')->nullable();
            $table->string('osoba_kontaktowa')->nullable();
            $table->string('telefon_kontaktowy')->nullable();
            $table->string('kod_pocztowy', 10)->nullable();
            $table->string('miasto')->nullable();

            // Lokalizacja GPS
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Dodatkowe informacje
            $table->text('uwagi_dostawy')->nullable();
            $table->text('uwagi_kierowcy')->nullable();
            $table->integer('kolejnosc_dostawy')->default(0);
            $table->decimal('dystans_km', 8, 2)->nullable();
            $table->integer('czas_dojazdu_min')->nullable();

            $table->timestamps();

            // Indeksy
            $table->index(['data_dostawy', 'status']);
            $table->index(['driver_id', 'data_dostawy']);
            $table->index(['production_order_id']);
            $table->index('kolejnosc_dostawy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
