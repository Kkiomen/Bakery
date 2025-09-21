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
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('production_order_item_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nazwa_produktu');
            $table->integer('ilosc');
            $table->string('jednostka', 20);
            $table->integer('ilosc_dostarczona')->default(0);
            $table->decimal('waga_kg', 8, 3)->nullable();
            $table->text('uwagi')->nullable();
            $table->enum('status', ['oczekujacy', 'przygotowany', 'dostarczony', 'brakuje', 'uszkodzony'])->default('oczekujacy');
            $table->timestamps();

            // Indeksy
            $table->index(['delivery_id', 'status']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
