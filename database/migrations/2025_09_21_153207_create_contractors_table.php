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
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa')->comment('Nazwa kontrahenta');
            $table->string('nip')->nullable()->comment('NIP kontrahenta');
            $table->string('regon')->nullable()->comment('REGON kontrahenta');
            $table->string('adres')->comment('Adres kontrahenta');
            $table->string('kod_pocztowy', 10)->comment('Kod pocztowy');
            $table->string('miasto')->comment('Miasto');
            $table->string('telefon')->nullable()->comment('Telefon kontaktowy');
            $table->string('email')->nullable()->comment('Email kontaktowy');
            $table->string('osoba_kontaktowa')->nullable()->comment('Osoba kontaktowa');
            $table->string('telefon_kontaktowy')->nullable()->comment('Telefon osoby kontaktowej');
            $table->enum('typ', ['klient', 'dostawca', 'obydwa'])->default('klient')->comment('Typ kontrahenta');
            $table->boolean('aktywny')->default(true)->comment('Czy kontrahent jest aktywny');
            $table->text('uwagi')->nullable()->comment('Dodatkowe uwagi');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Szerokość geograficzna');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Długość geograficzna');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
