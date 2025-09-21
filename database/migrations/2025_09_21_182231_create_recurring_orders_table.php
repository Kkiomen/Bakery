<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('b2_b_client_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('Nazwa szablonu zamówienia');
            $table->text('description')->nullable()->comment('Opis zamówienia cyklicznego');

            // Harmonogram
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'custom'])->comment('Częstotliwość');
            $table->json('schedule_config')->comment('Konfiguracja harmonogramu');
            $table->date('start_date')->comment('Data rozpoczęcia');
            $table->date('end_date')->nullable()->comment('Data zakończenia');

            // Szczegóły zamówienia
            $table->json('order_items')->comment('Produkty w zamówieniu');
            $table->decimal('estimated_total', 10, 2)->comment('Szacowana wartość');

            // Preferencje dostawy
            $table->string('delivery_address')->comment('Adres dostawy');
            $table->string('delivery_postal_code', 10)->comment('Kod pocztowy dostawy');
            $table->string('delivery_city')->comment('Miasto dostawy');
            $table->text('delivery_notes')->nullable()->comment('Uwagi do dostawy');
            $table->time('preferred_delivery_time_from')->nullable();
            $table->time('preferred_delivery_time_to')->nullable();

            // Ustawienia
            $table->boolean('auto_confirm')->default(false)->comment('Automatyczne potwierdzanie');
            $table->integer('days_before_notification')->default(1)->comment('Dni przed powiadomieniem');
            $table->boolean('is_active')->default(true)->comment('Czy aktywne');

            // Statystyki
            $table->integer('total_generated')->default(0)->comment('Wygenerowane zamówienia');
            $table->timestamp('last_generated_at')->nullable()->comment('Ostatnie wygenerowanie');
            $table->timestamp('next_generation_at')->nullable()->comment('Następne wygenerowanie');

            $table->timestamps();

            $table->index(['b2_b_client_id', 'is_active']);
            $table->index(['next_generation_at', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_orders');
    }
};
