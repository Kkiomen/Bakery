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
        Schema::create('b2_b_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->comment('Numer zamówienia');
            $table->foreignId('b2_b_client_id')->constrained('b2_b_clients')->onDelete('cascade');

            // Daty
            $table->date('order_date')->comment('Data złożenia zamówienia');
            $table->date('delivery_date')->comment('Planowana data dostawy');
            $table->time('delivery_time_from')->nullable()->comment('Czas dostawy od');
            $table->time('delivery_time_to')->nullable()->comment('Czas dostawy do');

            // Status zamówienia
            $table->enum('status', [
                'draft', 'pending', 'confirmed', 'in_production',
                'ready', 'shipped', 'delivered', 'cancelled', 'returned'
            ])->default('pending')->comment('Status zamówienia');

            // Typ zamówienia
            $table->enum('order_type', ['one_time', 'recurring', 'standing'])
                  ->default('one_time')->comment('Typ zamówienia');
            $table->json('recurring_settings')->nullable()->comment('Ustawienia zamówień cyklicznych');

            // Adres dostawy
            $table->string('delivery_address')->comment('Adres dostawy');
            $table->string('delivery_postal_code', 10)->comment('Kod pocztowy dostawy');
            $table->string('delivery_city')->comment('Miasto dostawy');
            $table->text('delivery_notes')->nullable()->comment('Uwagi do dostawy');

            // Finansowe
            $table->decimal('subtotal', 10, 2)->default(0)->comment('Wartość netto');
            $table->decimal('tax_amount', 10, 2)->default(0)->comment('Kwota VAT');
            $table->decimal('delivery_cost', 10, 2)->default(0)->comment('Koszt dostawy');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Kwota rabatu');
            $table->decimal('total_amount', 10, 2)->default(0)->comment('Kwota brutto');

            // Płatność
            $table->enum('payment_method', ['transfer', 'card', 'cash', 'credit'])
                  ->default('transfer')->comment('Sposób płatności');
            $table->enum('payment_status', ['pending', 'paid', 'overdue', 'cancelled'])
                  ->default('pending')->comment('Status płatności');
            $table->date('payment_due_date')->nullable()->comment('Termin płatności');

            // Dodatkowe informacje
            $table->text('customer_notes')->nullable()->comment('Uwagi klienta');
            $table->text('internal_notes')->nullable()->comment('Uwagi wewnętrzne');
            $table->json('special_requirements')->nullable()->comment('Specjalne wymagania');

            // Tracking
            $table->timestamp('confirmed_at')->nullable()->comment('Data potwierdzenia');
            $table->timestamp('production_started_at')->nullable()->comment('Data rozpoczęcia produkcji');
            $table->timestamp('shipped_at')->nullable()->comment('Data wysłania');
            $table->timestamp('delivered_at')->nullable()->comment('Data dostawy');

            $table->timestamps();

            // Indeksy
            $table->index(['b2_b_client_id', 'status']);
            $table->index(['delivery_date', 'status']);
            $table->index(['order_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2_b_orders');
    }
};
