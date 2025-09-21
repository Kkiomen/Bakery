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
        Schema::create('production_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('ilosc'); // Ilość do wyprodukowania
            $table->string('jednostka')->default('szt'); // Jednostka (szt, kg, itp.)
            $table->integer('ilosc_wyprodukowana')->default(0); // Ile już wyprodukowano
            $table->enum('status', ['oczekujace', 'w_produkcji', 'zakonczone'])->default('oczekujace');
            $table->text('uwagi')->nullable(); // Uwagi do konkretnej pozycji
            $table->timestamps();

            $table->index(['production_order_id', 'status']);
            $table->index(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_order_items');
    }
};
