<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🧹 Czyszczenie bazy danych...');

        // Wyłącz sprawdzanie kluczy obcych (SQLite)
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Wyczyść tabele w odpowiedniej kolejności
        $tables = [
            'b2_b_pricings',
            'b2_b_clients',
            'production_order_items',
            'production_orders',
            'recipe_materials',
            'recipes',
            'material_substitutes',
            'materials',
            'product_images',
            'products',
            'categories',
            'contractors',
            'deliveries',
            'users'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }

        // Włącz z powrotem sprawdzanie kluczy obcych (SQLite)
        DB::statement('PRAGMA foreign_keys = ON;');

        $this->command->info('✨ Tworzenie danych testowych...');

        // Utwórz użytkowników administracyjnych
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Admin Bakery',
            'email_verified_at' => now(),
            'email' => 'admin@bakery.local',
            'password' => Hash::make('admin'),
            'remember_token' => Str::random(10),
        ]);

        $this->call([
            // 1. PODSTAWOWE STRUKTURY
            CategorySeeder::class,           // Kategorie produktów (musi być pierwsze)
            ProductSeeder::class,            // Produkty (potrzebuje kategorii)
            MaterialSeeder::class,           // Materiały/składniki

            // 2. PRZEPISY I RELACJE
            RecipeSeeder::class,             // Przepisy
            AssignRecipesToProductsSeeder::class,  // Przypisanie przepisów do produktów
            AllProductsRecipesSeeder::class, // Przepisy dla produktów bez przepisów
            MaterialSubstitutesSeeder::class, // Zamienniki składników
            AdditionalMaterialSubstitutesSeeder::class, // Dodatkowe zamienniki

            // 3. KONTRAHENCI I DOSTAWY
            ContractorSeeder::class,         // Kontrahenci/dostawcy
            DeliverySeeder::class,           // Dostawy

            // 4. PRODUKCJA
            ProductionOrderSeeder::class,    // Zlecenia produkcyjne
            UpdateProductionOrderItemsSeeder::class, // Aktualizacja pozycji zleceń

            // 5. SYSTEM B2B
            B2BClientSeeder::class,          // Klienci B2B (musi być przed cenami)
            B2BQuantityDiscountSeeder::class, // Rabaty ilościowe i ceny B2B (nowa wersja)

            // 6. ADMINISTRACJA
            AdminUserSeeder::class,          // Administratorzy systemu
        ]);

        $this->command->info('🎉 Baza danych została wypełniona danymi testowymi!');
        $this->command->info('📊 Utworzono:');
        $this->command->info('   - Kategorie: ' . \App\Models\Category::count());
        $this->command->info('   - Produkty: ' . \App\Models\Product::count());
        $this->command->info('   - Materiały: ' . \App\Models\Material::count());
        $this->command->info('   - Przepisy: ' . \App\Models\Recipe::count());
        $this->command->info('   - Klienci B2B: ' . \App\Models\B2BClient::count());
        $this->command->info('   - Cenniki B2B: ' . \App\Models\B2BPricing::count());
        $this->command->info('   - Kontrahenci: ' . \App\Models\Contractor::count());
        $this->command->info('   - Zlecenia produkcyjne: ' . \App\Models\ProductionOrder::count());
    }
}
