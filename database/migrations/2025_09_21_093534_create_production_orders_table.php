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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('numer_zlecenia')->unique(); // Automatycznie generowany numer
            $table->string('nazwa'); // Nazwa zlecenia np. "Zamówienie sklep ABC"
            $table->text('opis')->nullable(); // Dodatkowy opis zlecenia
            $table->date('data_produkcji'); // Na który dzień ma być wyprodukowane
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kto zlecił
            $table->enum('status', ['oczekujace', 'w_produkcji', 'zakonczone', 'anulowane'])->default('oczekujace');
            $table->enum('priorytet', ['niski', 'normalny', 'wysoki', 'pilny'])->default('normalny');
            $table->enum('typ_zlecenia', ['wewnetrzne', 'sklep', 'b2b', 'hotel', 'inne'])->default('wewnetrzne');
            $table->string('klient')->nullable(); // Nazwa klienta/sklepu
            $table->text('uwagi')->nullable(); // Dodatkowe uwagi
            $table->timestamp('data_rozpoczecia')->nullable(); // Kiedy rozpoczęto produkcję
            $table->timestamp('data_zakonczenia')->nullable(); // Kiedy zakończono produkcję
            $table->timestamps();

            $table->index(['data_produkcji', 'status']);
            $table->index(['user_id', 'data_produkcji']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
