<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\RecipeStep;
use App\Models\Material;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createBulkiPszenne();
        $this->createChlebZytni();
        $this->createCiastoDrozdowe();
    }

    private function createBulkiPszenne()
    {
        // Receptura 1: Bułki pszenne
        $bulkiPszenne = Recipe::create([
            'kod' => 'REC-BULKA-001',
            'nazwa' => 'Bułki pszenne klasyczne',
            'opis' => 'Tradycyjne bułki pszenne na drożdżach',
            'product_id' => null,
            'ilosc_porcji' => 20,
            'waga_jednostkowa_g' => 60.00,
            'czas_przygotowania_min' => 30,
            'czas_wypiekania_min' => 15,
            'czas_calkowity_min' => 180,
            'temperatura_c' => 220,
            'instrukcje_wypiekania' => 'Piec z parą przez pierwsze 5 minut',
            'poziom_trudnosci' => 'łatwy',
            'kategoria' => 'bułki',
            'uwagi' => 'Receptura sprawdzona i przetestowana',
            'wskazowki' => 'Ważne jest odpowiednie wyrastanie ciasta',
            'aktywny' => true,
            'testowany' => true,
            'autor' => 'Mistrz Piekarz',
            'wersja' => '1.0',
        ]);

        $this->addMaterialsToBulki($bulkiPszenne);
        $this->addStepsToBulki($bulkiPszenne);
    }

    private function addMaterialsToBulki($recipe)
    {
        $maka = Material::where('kod', 'MAK-001')->first();
        $drozdze = Material::where('kod', 'DRO-001')->first();
        $cukier = Material::where('kod', 'CUK-001')->first();
        $sol = Material::where('kod', 'DOD-001')->first();
        $mleko = Material::where('kod', 'NAB-001')->first();
        $maslo = Material::where('kod', 'TLU-001')->first();
        $jajka = Material::where('kod', 'JAJ-001')->first();

        if ($maka) $recipe->addMaterial($maka, 1.0, 'kg', ['kolejnosc' => 1]);
        if ($drozdze) $recipe->addMaterial($drozdze, 0.025, 'kg', ['kolejnosc' => 2, 'sposob_przygotowania' => 'rozpuszczone w mleku']);
        if ($cukier) $recipe->addMaterial($cukier, 0.05, 'kg', ['kolejnosc' => 3]);
        if ($sol) $recipe->addMaterial($sol, 0.015, 'kg', ['kolejnosc' => 4]);
        if ($mleko) $recipe->addMaterial($mleko, 0.3, 'l', ['kolejnosc' => 5, 'temperatura_c' => 37]);
        if ($maslo) $recipe->addMaterial($maslo, 0.08, 'kg', ['kolejnosc' => 6, 'sposob_przygotowania' => 'roztopione']);
        if ($jajka) $recipe->addMaterial($jajka, 2, 'szt', ['kolejnosc' => 7, 'uwagi' => '1 do ciasta + 1 do smarowania']);
    }

    private function addStepsToBulki($recipe)
    {
        $steps = [
            [
                'kolejnosc' => 1, 'typ' => 'przygotowanie', 'nazwa' => 'Przygotowanie składników',
                'opis' => 'Odważyć wszystkie składniki. Mleko podgrzać do 37°C. Masło roztopić.',
                'czas_min' => 10, 'narzedzia' => 'waga, termometr, rondelek',
                'wskazowki' => 'Mleko nie może być za gorące - zabije drożdże',
                'kryteria_oceny' => 'Mleko ma temperaturę 35-38°C',
            ],
            [
                'kolejnosc' => 2, 'typ' => 'mieszanie', 'nazwa' => 'Przygotowanie rozczyny drożdżowej',
                'opis' => 'Drożdże rozpuścić w ciepłym mleku z dodatkiem cukru. Odstawić na 10 minut.',
                'czas_min' => 10, 'temperatura_c' => 37,
                'wskazowki' => 'Rozczyń powinien się spienić - znak że drożdże są aktywne',
                'kryteria_oceny' => 'Rozczyń pieni się i pachnie drożdżami',
            ],
            [
                'kolejnosc' => 3, 'typ' => 'wyrabianie', 'nazwa' => 'Wyrabianie ciasta',
                'opis' => 'Wyrabiać ciasto na stolnicy przez 8-10 minut do uzyskania gładkiej, elastycznej konsystencji.',
                'czas_min' => 10, 'narzedzia' => 'stolnica, skrobaczka',
                'wskazowki' => 'Ciasto powinno być gładkie i nie kleić się do rąk',
                'kryteria_oceny' => 'Ciasto gładkie, elastyczne, nie klei się',
            ],
            [
                'kolejnosc' => 4, 'typ' => 'wyrastanie', 'nazwa' => 'Pierwszy wzrost',
                'opis' => 'Ciasto włożyć do natłuszczonej miski, przykryć ściereczką. Odstawić w ciepłe miejsce.',
                'czas_min' => 60, 'temperatura_c' => 28, 'wilgotnosc_proc' => 75,
                'wskazowki' => 'Ciasto powinno podwoić objętość',
                'kryteria_oceny' => 'Ciasto podwaja objętość',
            ],
            [
                'kolejnosc' => 5, 'typ' => 'formowanie', 'nazwa' => 'Formowanie bułek',
                'opis' => 'Ciasto przebić, podzielić na 20 części po 60g. Uformować kulki.',
                'czas_min' => 15, 'narzedzia' => 'waga, nóż, stolnica',
                'wskazowki' => 'Każda bułka powinna mieć równą wagę',
                'kryteria_oceny' => 'Równe kulki, gładka powierzchnia',
            ],
            [
                'kolejnosc' => 6, 'typ' => 'odpoczynek', 'nazwa' => 'Drugi wzrost',
                'opis' => 'Bułki ułożyć na blasze, przykryć. Odstawić do wyrośnięcia.',
                'czas_min' => 45, 'temperatura_c' => 28, 'wilgotnosc_proc' => 75,
                'wskazowki' => 'Bułki powinny zwiększyć objętość o 50%',
                'kryteria_oceny' => 'Bułki wyraźnie większe, sprężyste',
            ],
            [
                'kolejnosc' => 7, 'typ' => 'wypiekanie', 'nazwa' => 'Wypiekanie',
                'opis' => 'Posmarować roztrzepanym jajkiem. Piec w 220°C przez 15 minut.',
                'czas_min' => 15, 'temperatura_c' => 220,
                'wskazowki' => 'Pierwsze 5 minut z parą wodną',
                'kryteria_oceny' => 'Złoty kolor, puste brzmienie po opukaniu',
            ],
            [
                'kolejnosc' => 8, 'typ' => 'chłodzenie', 'nazwa' => 'Chłodzenie',
                'opis' => 'Wyjąć z piekarnika, przełożyć na kratkę do chłodzenia.',
                'czas_min' => 30, 'temperatura_c' => 25,
                'wskazowki' => 'Nie pakować gdy są gorące',
                'kryteria_oceny' => 'Całkowicie ostudzone',
            ],
        ];

        foreach ($steps as $stepData) {
            $recipe->addStep(array_merge($stepData, [
                'obowiazkowy' => true,
                'automatyczny' => false,
            ]));
        }
    }

    private function createChlebZytni()
    {
        $chleb = Recipe::create([
            'kod' => 'REC-CHLEB-001',
            'nazwa' => 'Chleb żytni na żurek',
            'opis' => 'Tradycyjny chleb żytni z żurkiem',
            'ilosc_porcji' => 2,
            'waga_jednostkowa_g' => 800.00,
            'czas_przygotowania_min' => 45,
            'czas_wypiekania_min' => 50,
            'czas_calkowity_min' => 480,
            'temperatura_c' => 200,
            'poziom_trudnosci' => 'trudny',
            'kategoria' => 'chleby',
            'aktywny' => true,
            'testowany' => false,
            'autor' => 'Mistrz Piekarz',
            'wersja' => '1.2',
        ]);

        $makaZytnia = Material::where('kod', 'MAK-002')->first();
        $maka = Material::where('kod', 'MAK-001')->first();
        $sol = Material::where('kod', 'DOD-001')->first();

        if ($makaZytnia) $chleb->addMaterial($makaZytnia, 1.2, 'kg', ['kolejnosc' => 1]);
        if ($maka) $chleb->addMaterial($maka, 0.3, 'kg', ['kolejnosc' => 2]);
        if ($sol) $chleb->addMaterial($sol, 0.025, 'kg', ['kolejnosc' => 3]);
    }

    private function createCiastoDrozdowe()
    {
        $ciasto = Recipe::create([
            'kod' => 'REC-ROGAL-001',
            'nazwa' => 'Ciasto drożdżowe na rogale',
            'opis' => 'Uniwersalne ciasto drożdżowe do rogali z różnymi nadzieniami',
            'ilosc_porcji' => 12,
            'waga_jednostkowa_g' => 120.00,
            'czas_przygotowania_min' => 40,
            'czas_wypiekania_min' => 20,
            'czas_calkowity_min' => 240,
            'temperatura_c' => 190,
            'poziom_trudnosci' => 'średni',
            'kategoria' => 'rogale',
            'aktywny' => true,
            'testowany' => true,
            'autor' => 'Piekarz Junior',
            'wersja' => '2.0',
        ]);

        $maka = Material::where('kod', 'MAK-001')->first();
        $drozdze = Material::where('kod', 'DRO-001')->first();
        $cukier = Material::where('kod', 'CUK-001')->first();
        $maslo = Material::where('kod', 'TLU-001')->first();

        if ($maka) $ciasto->addMaterial($maka, 0.8, 'kg', ['kolejnosc' => 1]);
        if ($drozdze) $ciasto->addMaterial($drozdze, 0.02, 'kg', ['kolejnosc' => 2]);
        if ($cukier) $ciasto->addMaterial($cukier, 0.1, 'kg', ['kolejnosc' => 3]);
        if ($maslo) $ciasto->addMaterial($maslo, 0.15, 'kg', ['kolejnosc' => 4]);
    }
}
