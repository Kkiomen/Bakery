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
        Schema::create('b2_b_clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->comment('Nazwa firmy');
            $table->string('nip')->unique()->comment('NIP firmy');
            $table->string('regon')->nullable()->comment('REGON firmy');
            $table->string('email')->unique()->comment('Email logowania');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->comment('Hasło');

            // Dane firmowe
            $table->string('address')->comment('Adres firmy');
            $table->string('postal_code', 10)->comment('Kod pocztowy');
            $table->string('city')->comment('Miasto');
            $table->string('phone')->nullable()->comment('Telefon główny');
            $table->string('website')->nullable()->comment('Strona internetowa');

            // Osoba kontaktowa
            $table->string('contact_person')->comment('Osoba kontaktowa');
            $table->string('contact_phone')->nullable()->comment('Telefon kontaktowy');
            $table->string('contact_email')->nullable()->comment('Email kontaktowy');

            // Typ działalności
            $table->enum('business_type', ['hotel', 'restaurant', 'cafe', 'shop', 'catering', 'other'])
                  ->default('other')->comment('Typ działalności');
            $table->text('business_description')->nullable()->comment('Opis działalności');

            // Preferencje dostaw
            $table->json('delivery_addresses')->nullable()->comment('Adresy dostaw (JSON)');
            $table->enum('preferred_delivery_time', ['morning', 'afternoon', 'evening', 'flexible'])
                  ->default('flexible')->comment('Preferowany czas dostaw');
            $table->json('delivery_days')->nullable()->comment('Preferowane dni dostaw (JSON)');

            // Status konta
            $table->enum('status', ['pending', 'active', 'suspended', 'inactive'])
                  ->default('pending')->comment('Status konta');
            $table->enum('pricing_tier', ['standard', 'bronze', 'silver', 'gold', 'platinum'])
                  ->default('standard')->comment('Poziom cenowy');

            // Limity kredytowe
            $table->decimal('credit_limit', 10, 2)->default(0)->comment('Limit kredytowy');
            $table->decimal('current_balance', 10, 2)->default(0)->comment('Aktualne saldo');

            // Dodatkowe informacje
            $table->text('notes')->nullable()->comment('Notatki wewnętrzne');
            $table->date('contract_start_date')->nullable()->comment('Data rozpoczęcia współpracy');
            $table->date('contract_end_date')->nullable()->comment('Data zakończenia współpracy');

            // Ustawienia powiadomień
            $table->boolean('email_notifications')->default(true)->comment('Powiadomienia email');
            $table->boolean('sms_notifications')->default(false)->comment('Powiadomienia SMS');

            $table->rememberToken();
            $table->timestamps();

            // Indeksy
            $table->index(['status', 'pricing_tier']);
            $table->index(['business_type', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2_b_clients');
    }
};
