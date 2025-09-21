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
        Schema::create('delivery_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->longText('signature_data'); // Base64 encoded signature
            $table->string('signer_name');
            $table->string('signer_position')->nullable();
            $table->datetime('signature_date');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Lokalizacja GPS gdzie podpisano
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->text('uwagi')->nullable();
            $table->timestamps();

            // Indeksy
            $table->index('delivery_id');
            $table->index('signature_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_signatures');
    }
};
