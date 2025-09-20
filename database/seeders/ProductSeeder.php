<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utworzenie kategorii
        $categories = [
            [
                'nazwa' => 'Chleby',
                'opis' => 'Różne rodzaje chleba',
                'aktywny' => true,
            ],
            [
                'nazwa' => 'Bułki',
                'opis' => 'Bułki i pieczywo drobne',
                'aktywny' => true,
            ],
            [
                'nazwa' => 'Ciasta',
                'opis' => 'Ciasta i desery',
                'aktywny' => true,
            ],
            [
                'nazwa' => 'Ciastka',
                'opis' => 'Ciastka i herbatniki',
                'aktywny' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Pobranie ID kategorii
        $chlebyId = Category::where('nazwa', 'Chleby')->first()->id;
        $bulkiId = Category::where('nazwa', 'Bułki')->first()->id;
        $ciastaId = Category::where('nazwa', 'Ciasta')->first()->id;
        $ciastkaId = Category::where('nazwa', 'Ciastka')->first()->id;

        // Utworzenie produktów
        $products = [
            [
                'sku' => 'CHLEB-001',
                'ean' => '5901234567890',
                'nazwa' => 'Chleb żytni razowy',
                'opis' => 'Tradycyjny chleb żytni na żurawinie naturalnym. Bogaty w błonnik i witaminy.',
                'kategoria_id' => $chlebyId,
                'waga_g' => 500,
                'jednostka_sprzedazy' => 'szt',
                'zawartosc_opakowania' => null,
                'alergeny' => ['gluten'],
                'wartosci_odzywcze' => [
                    'kcal' => 220,
                    'bialko_g' => 7.5,
                    'tluszcz_g' => 1.2,
                    'wegle_g' => 45.8,
                ],
                'stawka_vat' => '5',
                'cena_netto_gr' => 450,
                'aktywny' => true,
                'meta_title' => 'Chleb żytni razowy - piekarnia',
                'meta_description' => 'Świeży chleb żytni razowy z naszej piekarni. Naturalny żurawiec, bez konserwantów.',
            ],
            [
                'sku' => 'CHLEB-002',
                'ean' => '5901234567891',
                'nazwa' => 'Chleb pszenny',
                'opis' => 'Klasyczny chleb pszenny o delikatnym smaku.',
                'kategoria_id' => $chlebyId,
                'waga_g' => 750,
                'jednostka_sprzedazy' => 'szt',
                'zawartosc_opakowania' => null,
                'alergeny' => ['gluten'],
                'wartosci_odzywcze' => [
                    'kcal' => 265,
                    'bialko_g' => 8.9,
                    'tluszcz_g' => 3.2,
                    'wegle_g' => 49.8,
                ],
                'stawka_vat' => '5',
                'cena_netto_gr' => 380,
                'aktywny' => true,
                'meta_title' => null,
                'meta_description' => null,
            ],
            [
                'sku' => 'BULKA-001',
                'ean' => null,
                'nazwa' => 'Bułka kajzerka',
                'opis' => 'Tradycyjna bułka kajzerka z chrupiącą skórką.',
                'kategoria_id' => $bulkiId,
                'waga_g' => 80,
                'jednostka_sprzedazy' => 'szt',
                'zawartosc_opakowania' => null,
                'alergeny' => ['gluten', 'jajka'],
                'wartosci_odzywcze' => [
                    'kcal' => 280,
                    'bialko_g' => 9.2,
                    'tluszcz_g' => 4.1,
                    'wegle_g' => 52.3,
                ],
                'stawka_vat' => '5',
                'cena_netto_gr' => 90,
                'aktywny' => true,
                'meta_title' => null,
                'meta_description' => null,
            ],
            [
                'sku' => 'BULKA-002',
                'ean' => '5901234567892',
                'nazwa' => 'Bułka grahamka',
                'opis' => 'Zdrowa bułka z mąki graham, bogata w błonnik.',
                'kategoria_id' => $bulkiId,
                'waga_g' => 70,
                'jednostka_sprzedazy' => 'szt',
                'zawartosc_opakowania' => null,
                'alergeny' => ['gluten'],
                'wartosci_odzywcze' => [
                    'kcal' => 245,
                    'bialko_g' => 8.5,
                    'tluszcz_g' => 2.8,
                    'wegle_g' => 47.2,
                ],
                'stawka_vat' => '5',
                'cena_netto_gr' => 110,
                'aktywny' => true,
                'meta_title' => null,
                'meta_description' => null,
            ],
            [
                'sku' => 'CIASTO-001',
                'ean' => null,
                'nazwa' => 'Sernik klasyczny',
                'opis' => 'Domowy sernik na kruchym cieście z twarogiem.',
                'kategoria_id' => $ciastaId,
                'waga_g' => 1200,
                'jednostka_sprzedazy' => 'szt',
                'zawartosc_opakowania' => 8,
                'alergeny' => ['gluten', 'mleko', 'jajka'],
                'wartosci_odzywcze' => [
                    'kcal' => 320,
                    'bialko_g' => 12.5,
                    'tluszcz_g' => 18.2,
                    'wegle_g' => 28.7,
                ],
                'stawka_vat' => '8',
                'cena_netto_gr' => 2800,
                'aktywny' => true,
                'meta_title' => null,
                'meta_description' => null,
            ],
            [
                'sku' => 'CIASTKO-001',
                'ean' => '5901234567893',
                'nazwa' => 'Ciastka owsiane',
                'opis' => 'Chrupiące ciastka owsiane z rodzynkami.',
                'kategoria_id' => $ciastkaId,
                'waga_g' => 300,
                'jednostka_sprzedazy' => 'opak',
                'zawartosc_opakowania' => 12,
                'alergeny' => ['gluten', 'mleko'],
                'wartosci_odzywcze' => [
                    'kcal' => 450,
                    'bialko_g' => 8.2,
                    'tluszcz_g' => 18.5,
                    'wegle_g' => 65.3,
                ],
                'stawka_vat' => '8',
                'cena_netto_gr' => 850,
                'aktywny' => true,
                'meta_title' => null,
                'meta_description' => null,
            ],
            [
                'sku' => 'CHLEB-003',
                'ean' => null,
                'nazwa' => 'Chleb bezglutenowy',
                'opis' => 'Chleb dla osób z nietolerancją glutenu, z mąki ryżowej i kukurydzianej.',
                'kategoria_id' => $chlebyId,
                'waga_g' => 400,
                'jednostka_sprzedazy' => 'szt',
                'zawartosc_opakowania' => null,
                'alergeny' => [],
                'wartosci_odzywcze' => [
                    'kcal' => 240,
                    'bialko_g' => 4.2,
                    'tluszcz_g' => 2.8,
                    'wegle_g' => 52.1,
                ],
                'stawka_vat' => '5',
                'cena_netto_gr' => 680,
                'aktywny' => true,
                'meta_title' => 'Chleb bezglutenowy - piekarnia',
                'meta_description' => 'Chleb bezglutenowy dla osób z celiakią. Świeży, bez glutenu.',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Dodanie zamienników
        $chleb1 = Product::where('sku', 'CHLEB-001')->first();
        $chleb2 = Product::where('sku', 'CHLEB-002')->first();
        $chleb3 = Product::where('sku', 'CHLEB-003')->first();
        $bulka1 = Product::where('sku', 'BULKA-001')->first();
        $bulka2 = Product::where('sku', 'BULKA-002')->first();

        // Chleby jako zamienniki
        if ($chleb1 && $chleb2) {
            $chleb1->addSubstitute($chleb2, 1, 'Alternatywa pszenna');
        }
        if ($chleb1 && $chleb3) {
            $chleb1->addSubstitute($chleb3, 2, 'Opcja bezglutenowa');
        }

        // Bułki jako zamienniki
        if ($bulka1 && $bulka2) {
            $bulka1->addSubstitute($bulka2, 1, 'Zdrowsza opcja');
        }
    }
}
