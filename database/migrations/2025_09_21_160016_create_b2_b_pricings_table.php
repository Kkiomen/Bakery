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
        Schema::create('b2_b_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Poziomy cenowe
            $table->enum('pricing_tier', ['standard', 'bronze', 'silver', 'gold', 'platinum'])
                  ->default('standard')->comment('Poziom cenowy');

            // Ceny
            $table->decimal('price_net', 8, 2)->comment('Cena netto');
            $table->decimal('price_gross', 8, 2)->comment('Cena brutto');
            $table->decimal('tax_rate', 5, 2)->default(23.00)->comment('Stawka VAT');

            // Rabaty ilościowe
            $table->integer('min_quantity')->default(1)->comment('Minimalna ilość');
            $table->integer('max_quantity')->nullable()->comment('Maksymalna ilość (null = bez limitu)');
            $table->decimal('discount_percent', 5, 2)->default(0)->comment('Procent rabatu');

            // Warunki specjalne
            $table->date('valid_from')->nullable()->comment('Ważne od');
            $table->date('valid_to')->nullable()->comment('Ważne do');
            $table->json('conditions')->nullable()->comment('Dodatkowe warunki (JSON)');

            // Dostępność
            $table->boolean('is_active')->default(true)->comment('Czy aktywne');
            $table->integer('priority')->default(0)->comment('Priorytet (wyższy = ważniejszy)');

            // Dla konkretnych klientów (opcjonalnie)
            $table->foreignId('b2_b_client_id')->nullable()
                  ->constrained('b2_b_clients')->onDelete('cascade')
                  ->comment('Specjalne ceny dla konkretnego klienta');

            $table->timestamps();

            // Indeksy
            $table->index(['product_id', 'pricing_tier', 'is_active']);
            $table->index(['b2_b_client_id', 'is_active']);
            $table->index(['valid_from', 'valid_to']);

            // Unikalne kombinacje
            $table->unique(['product_id', 'pricing_tier', 'min_quantity', 'b2_b_client_id'], 'unique_pricing_rule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2_b_pricings');
    }
};
