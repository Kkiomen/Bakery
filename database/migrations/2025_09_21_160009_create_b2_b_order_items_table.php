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
        Schema::create('b2_b_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('b2_b_order_id')->constrained('b2_b_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Szczegóły produktu (snapshot w momencie zamówienia)
            $table->string('product_name')->comment('Nazwa produktu');
            $table->string('product_sku')->nullable()->comment('SKU produktu');
            $table->text('product_description')->nullable()->comment('Opis produktu');

            // Ilości
            $table->integer('quantity')->comment('Zamówiona ilość');
            $table->integer('delivered_quantity')->default(0)->comment('Dostarczona ilość');
            $table->string('unit', 20)->default('szt')->comment('Jednostka');
            $table->decimal('unit_weight', 8, 3)->nullable()->comment('Waga jednostkowa (kg)');

            // Ceny (snapshot w momencie zamówienia)
            $table->decimal('unit_price', 8, 2)->comment('Cena jednostkowa netto');
            $table->decimal('unit_price_gross', 8, 2)->comment('Cena jednostkowa brutto');
            $table->decimal('discount_percent', 5, 2)->default(0)->comment('Procent rabatu');
            $table->decimal('discount_amount', 8, 2)->default(0)->comment('Kwota rabatu');
            $table->decimal('line_total', 10, 2)->comment('Wartość pozycji netto');
            $table->decimal('line_total_gross', 10, 2)->comment('Wartość pozycji brutto');

            // VAT
            $table->decimal('tax_rate', 5, 2)->default(23.00)->comment('Stawka VAT');
            $table->decimal('tax_amount', 8, 2)->default(0)->comment('Kwota VAT');

            // Status pozycji
            $table->enum('status', ['pending', 'confirmed', 'in_production', 'ready', 'delivered', 'cancelled'])
                  ->default('pending')->comment('Status pozycji');

            // Dodatkowe informacje
            $table->text('notes')->nullable()->comment('Uwagi do pozycji');
            $table->json('customizations')->nullable()->comment('Personalizacje (JSON)');
            $table->date('requested_delivery_date')->nullable()->comment('Żądana data dostawy');

            // Produkcja
            $table->foreignId('production_order_item_id')->nullable()
                  ->constrained('production_order_items')->onDelete('set null')
                  ->comment('Powiązana pozycja zlecenia produkcyjnego');

            $table->timestamps();

            // Indeksy
            $table->index(['b2_b_order_id', 'status']);
            $table->index(['product_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2_b_order_items');
    }
};
