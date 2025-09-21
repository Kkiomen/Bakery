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

        $this->addStepsToBulki($bulkiPszenne);
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

        $createdSteps = [];
        foreach ($steps as $stepData) {
            $createdSteps[] = $recipe->addStep(array_merge($stepData, [
                'obowiazkowy' => true,
                'automatyczny' => false,
            ]));
        }

        // Dodaj składniki do odpowiednich kroków
        $this->addMaterialsToBulkiSteps($createdSteps);
    }

    private function addMaterialsToBulkiSteps($steps)
    {
        $maka = Material::where('kod', 'MAK-001')->first();
        $drozdze = Material::where('kod', 'DRO-001')->first();
        $cukier = Material::where('kod', 'CUK-001')->first();
        $sol = Material::where('kod', 'DOD-001')->first();
        $mleko = Material::where('kod', 'NAB-001')->first();
        $maslo = Material::where('kod', 'TLU-001')->first();
        $jajka = Material::where('kod', 'JAJ-001')->first();

        // Krok 1: Przygotowanie składników - wszystkie składniki
        if (isset($steps[0])) {
            if ($maka) $steps[0]->addMaterial($maka, 1.0, 'kg', ['kolejnosc' => 0]);
            if ($mleko) $steps[0]->addMaterial($mleko, 0.3, 'l', ['kolejnosc' => 1, 'temperatura_c' => 37]);
            if ($maslo) $steps[0]->addMaterial($maslo, 0.08, 'kg', ['kolejnosc' => 2, 'sposob_przygotowania' => 'roztopione']);
            if ($jajka) $steps[0]->addMaterial($jajka, 2, 'szt', ['kolejnosc' => 3, 'uwagi' => '1 do ciasta + 1 do smarowania']);
        }

        // Krok 2: Przygotowanie rozczyny drożdżowej - drożdże i cukier
        if (isset($steps[1])) {
            if ($drozdze) $steps[1]->addMaterial($drozdze, 0.025, 'kg', ['kolejnosc' => 0, 'sposob_przygotowania' => 'rozpuszczone w mleku']);
            if ($cukier) $steps[1]->addMaterial($cukier, 0.05, 'kg', ['kolejnosc' => 1, 'uwagi' => 'Do aktywacji drożdży']);
        }

        // Krok 3: Wyrabianie ciasta - sól
        if (isset($steps[2])) {
            if ($sol) $steps[2]->addMaterial($sol, 0.015, 'kg', ['kolejnosc' => 0]);
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

        $this->addStepsToChlebZytni($chleb);
    }

    private function addStepsToChlebZytni($recipe)
    {
        $steps = [
            [
                'kolejnosc' => 1, 'typ' => 'przygotowanie', 'nazwa' => 'Przygotowanie żurku',
                'opis' => 'Żurek przygotować wcześniej - fermentacja mąki żytniej przez 5-7 dni.',
                'czas_min' => 10080, 'temperatura_c' => 22,
                'wskazowki' => 'Żurek powinien mieć kwaśny zapach i gęstą konsystencję',
                'kryteria_oceny' => 'Żurek kwaśny, bez pleśni, gęsty',
            ],
            [
                'kolejnosc' => 2, 'typ' => 'mieszanie', 'nazwa' => 'Przygotowanie roczynu',
                'opis' => 'Połączyć mąkę żytnią z ciepłą wodą i żurkiem. Wymieszać do jednolitej masy.',
                'czas_min' => 15, 'temperatura_c' => 35,
                'wskazowki' => 'Rozczyn powinien mieć konsystencję gęstej śmietany',
                'kryteria_oceny' => 'Jednorodna masa bez grudek',
            ],
            [
                'kolejnosc' => 3, 'typ' => 'wyrastanie', 'nazwa' => 'Fermentacja roczynu',
                'opis' => 'Rozczyn przykryć wilgotną ściereczką i odstawić na 2-3 godziny.',
                'czas_min' => 150, 'temperatura_c' => 28, 'wilgotnosc_proc' => 80,
                'wskazowki' => 'Rozczyn powinien się spienić i zwiększyć objętość',
                'kryteria_oceny' => 'Rozczyn pęcznieje, ma kwaśny zapach',
            ],
            [
                'kolejnosc' => 4, 'typ' => 'wyrabianie', 'nazwa' => 'Wyrabianie ciasta',
                'opis' => 'Dodać mąkę pszenną, sól i wyrabiać ciasto przez 12-15 minut.',
                'czas_min' => 15, 'narzedzia' => 'stolnica, skrobaczka',
                'wskazowki' => 'Ciasto żytnie jest bardziej lepkie niż pszenne',
                'kryteria_oceny' => 'Ciasto jednolite, elastyczne ale lepkie',
            ],
            [
                'kolejnosc' => 5, 'typ' => 'wyrastanie', 'nazwa' => 'Pierwszy wzrost',
                'opis' => 'Ciasto włożyć do natłuszczonej miski, przykryć. Odstawić na 90 minut.',
                'czas_min' => 90, 'temperatura_c' => 28, 'wilgotnosc_proc' => 75,
                'wskazowki' => 'Ciasto żytnie rośnie wolniej niż pszenne',
                'kryteria_oceny' => 'Ciasto zwiększa objętość o 70-80%',
            ],
            [
                'kolejnosc' => 6, 'typ' => 'formowanie', 'nazwa' => 'Formowanie bochenków',
                'opis' => 'Ciasto przebić, podzielić na 2 części po 800g. Uformować bochenki.',
                'czas_min' => 20, 'narzedzia' => 'waga, nóż, stolnica',
                'wskazowki' => 'Formować delikatnie, nie przesuszać mąką',
                'kryteria_oceny' => 'Równe bochenki, gładka powierzchnia',
            ],
            [
                'kolejnosc' => 7, 'typ' => 'odpoczynek', 'nazwa' => 'Drugi wzrost',
                'opis' => 'Bochenki ułożyć w koszach lub na blasze, przykryć. Odstawić na 60 minut.',
                'czas_min' => 60, 'temperatura_c' => 28, 'wilgotnosc_proc' => 80,
                'wskazowki' => 'Bochenki powinny zwiększyć objętość o 40-50%',
                'kryteria_oceny' => 'Bochenki wyraźnie większe, sprężyste',
            ],
            [
                'kolejnosc' => 8, 'typ' => 'przygotowanie', 'nazwa' => 'Nacinanie bochenków',
                'opis' => 'Ostrym nożem naciąć bochenki w charakterystyczny wzór.',
                'czas_min' => 5, 'narzedzia' => 'ostry nóż lub żyletka',
                'wskazowki' => 'Nacięcia głębokie na 0,5cm, pewnym ruchem',
                'kryteria_oceny' => 'Równe, głębokie nacięcia',
            ],
            [
                'kolejnosc' => 9, 'typ' => 'wypiekanie', 'nazwa' => 'Wypiekanie',
                'opis' => 'Piec w 200°C przez 50 minut. Pierwsze 15 minut z parą.',
                'czas_min' => 50, 'temperatura_c' => 200,
                'wskazowki' => 'Para wodna zapewni chrupiącą skórkę',
                'kryteria_oceny' => 'Ciemnobrązowy kolor, puste brzmienie',
            ],
            [
                'kolejnosc' => 10, 'typ' => 'chłodzenie', 'nazwa' => 'Chłodzenie',
                'opis' => 'Wyjąć z piekarnika, przełożyć na kratkę. Studzić minimum 2 godziny.',
                'czas_min' => 120, 'temperatura_c' => 25,
                'wskazowki' => 'Chleb żytni musi całkowicie ostygnąć przed krojeniem',
                'kryteria_oceny' => 'Całkowicie ostudzone, twarda skórka',
            ],
        ];

        $createdSteps = [];
        foreach ($steps as $stepData) {
            $createdSteps[] = $recipe->addStep(array_merge($stepData, [
                'obowiazkowy' => true,
                'automatyczny' => false,
            ]));
        }

        // Dodaj składniki do odpowiednich kroków
        $this->addMaterialsToChlebZytniSteps($createdSteps);
    }

    private function addMaterialsToChlebZytniSteps($steps)
    {
        $makaZytnia = Material::where('kod', 'MAK-002')->first();
        $maka = Material::where('kod', 'MAK-001')->first();
        $woda = Material::where('kod', 'WOD-001')->first();
        $sol = Material::where('kod', 'DOD-001')->first();
        $drozdze = Material::where('kod', 'DRO-001')->first();

        // Krok 1: Przygotowanie żurku - mąka żytnia i woda
        if (isset($steps[0]) && $makaZytnia && $woda) {
            $steps[0]->addMaterial($makaZytnia, 0.5, 'kg', ['kolejnosc' => 0, 'uwagi' => 'Do żurku']);
            $steps[0]->addMaterial($woda, 0.6, 'l', ['kolejnosc' => 1, 'temperatura_c' => 22, 'uwagi' => 'Woda do żurku']);
        }

        // Krok 2: Przygotowanie roczynu - pozostała mąka żytnia i woda
        if (isset($steps[1]) && $makaZytnia && $woda) {
            $steps[1]->addMaterial($makaZytnia, 0.7, 'kg', ['kolejnosc' => 0, 'uwagi' => 'Pozostała mąka żytnia']);
            $steps[1]->addMaterial($woda, 0.2, 'l', ['kolejnosc' => 1, 'temperatura_c' => 35, 'uwagi' => 'Ciepła woda']);
        }

        // Krok 4: Wyrabianie ciasta - mąka pszenna, sól, opcjonalnie drożdże
        if (isset($steps[3]) && $maka && $sol) {
            $steps[3]->addMaterial($maka, 0.3, 'kg', ['kolejnosc' => 0, 'uwagi' => 'Do wzmocnienia ciasta']);
            $steps[3]->addMaterial($sol, 0.025, 'kg', ['kolejnosc' => 1]);
            if ($drozdze) {
                $steps[3]->addMaterial($drozdze, 0.01, 'kg', ['kolejnosc' => 2, 'opcjonalny' => true, 'uwagi' => 'Dla przyspieszenia']);
            }
        }
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

        $this->addStepsToCiastoDrozdowe($ciasto);
    }

    private function addStepsToCiastoDrozdowe($recipe)
    {
        $steps = [
            [
                'kolejnosc' => 1, 'typ' => 'przygotowanie', 'nazwa' => 'Przygotowanie składników',
                'opis' => 'Wszystkie składniki powinny mieć temperaturę pokojową. Masło rozmiękczone.',
                'czas_min' => 10, 'temperatura_c' => 22,
                'wskazowki' => 'Zimne składniki mogą zabić drożdże',
                'kryteria_oceny' => 'Wszystkie składniki w temperaturze pokojowej',
            ],
            [
                'kolejnosc' => 2, 'typ' => 'mieszanie', 'nazwa' => 'Przygotowanie rozczyny drożdżowej',
                'opis' => 'Drożdże rozpuścić w ciepłym mleku (37°C) z łyżką cukru. Odstawić na 10 minut.',
                'czas_min' => 10, 'temperatura_c' => 37,
                'wskazowki' => 'Rozczyń powinien się spienić - znak aktywności drożdży',
                'kryteria_oceny' => 'Rozczyń pieni się i pachnie drożdżami',
            ],
            [
                'kolejnosc' => 3, 'typ' => 'mieszanie', 'nazwa' => 'Łączenie składników',
                'opis' => 'Do mąki dodać pozostały cukier, sól, rozmiękczone masło i rozczyń drożdżowy.',
                'czas_min' => 5, 'narzedzia' => 'miska, łyżka drewniana',
                'wskazowki' => 'Mieszać od środka na zewnątrz',
                'kryteria_oceny' => 'Składniki równomiernie rozprowadzone',
            ],
            [
                'kolejnosc' => 4, 'typ' => 'wyrabianie', 'nazwa' => 'Wyrabianie ciasta',
                'opis' => 'Wyrabiać ciasto na stolnicy przez 10-12 minut do uzyskania gładkiej, elastycznej konsystencji.',
                'czas_min' => 12, 'narzedzia' => 'stolnica, skrobaczka',
                'wskazowki' => 'Ciasto powinno być gładkie, elastyczne i lekko lepkie',
                'kryteria_oceny' => 'Ciasto gładkie, elastyczne, nie rozrywa się',
            ],
            [
                'kolejnosc' => 5, 'typ' => 'wyrastanie', 'nazwa' => 'Pierwszy wzrost',
                'opis' => 'Ciasto włożyć do natłuszczonej miski, przykryć wilgotną ściereczką. Odstawić na 60 minut.',
                'czas_min' => 60, 'temperatura_c' => 28, 'wilgotnosc_proc' => 75,
                'wskazowki' => 'Ciasto powinno podwoić swoją objętość',
                'kryteria_oceny' => 'Ciasto podwaja objętość, sprężyste',
            ],
            [
                'kolejnosc' => 6, 'typ' => 'formowanie', 'nazwa' => 'Przebijanie i dzielenie',
                'opis' => 'Ciasto przebić, podzielić na 12 równych części po około 120g każda.',
                'czas_min' => 10, 'narzedzia' => 'waga, nóż, stolnica',
                'wskazowki' => 'Każda porcja powinna mieć równą wagę',
                'kryteria_oceny' => 'Równe porcje ciasta, waga 120g ±5g',
            ],
            [
                'kolejnosc' => 7, 'typ' => 'formowanie', 'nazwa' => 'Formowanie rogali',
                'opis' => 'Każdą porcję rozwałkować na trójkąt, nałożyć nadzienie, zwinąć w rogala.',
                'czas_min' => 20, 'narzedzia' => 'wałek, nóż',
                'wskazowki' => 'Rogale zwijać od szerokiej strony do wąskiej',
                'kryteria_oceny' => 'Równe rogale, dobrze zwinięte, szczelne',
            ],
            [
                'kolejnosc' => 8, 'typ' => 'odpoczynek', 'nazwa' => 'Drugi wzrost',
                'opis' => 'Rogale ułożyć na blasze wyłożonej papierem, przykryć. Odstawić na 30 minut.',
                'czas_min' => 30, 'temperatura_c' => 28, 'wilgotnosc_proc' => 75,
                'wskazowki' => 'Rogale powinny zwiększyć objętość o 40-50%',
                'kryteria_oceny' => 'Rogale wyraźnie większe, sprężyste',
            ],
            [
                'kolejnosc' => 9, 'typ' => 'przygotowanie', 'nazwa' => 'Smarowanie jajkiem',
                'opis' => 'Delikatnie posmarować rogale roztrzepanym jajkiem z odrobiną mleka.',
                'czas_min' => 5, 'narzedzia' => 'pędzelek, miska',
                'wskazowki' => 'Smarować delikatnie, żeby nie uszkodzić ciasta',
                'kryteria_oceny' => 'Równomiernie posmarowane, bez kałuż',
            ],
            [
                'kolejnosc' => 10, 'typ' => 'wypiekanie', 'nazwa' => 'Wypiekanie',
                'opis' => 'Piec w 190°C przez 18-20 minut do złotego koloru.',
                'czas_min' => 20, 'temperatura_c' => 190,
                'wskazowki' => 'Nie otwierać piekarnika przez pierwsze 15 minut',
                'kryteria_oceny' => 'Złoty kolor, puste brzmienie po opukaniu',
            ],
            [
                'kolejnosc' => 11, 'typ' => 'chłodzenie', 'nazwa' => 'Chłodzenie',
                'opis' => 'Wyjąć z piekarnika, przełożyć na kratkę do chłodzenia na 15 minut.',
                'czas_min' => 15, 'temperatura_c' => 25,
                'wskazowki' => 'Rogale najlepsze są lekko ciepłe',
                'kryteria_oceny' => 'Lekko ciepłe, chrupiąca skórka',
            ],
        ];

        $createdSteps = [];
        foreach ($steps as $stepData) {
            $createdSteps[] = $recipe->addStep(array_merge($stepData, [
                'obowiazkowy' => true,
                'automatyczny' => false,
            ]));
        }

        // Dodaj składniki do odpowiednich kroków
        $this->addMaterialsToCiastoDrozdoweSteps($createdSteps);
    }

    private function addMaterialsToCiastoDrozdoweSteps($steps)
    {
        $maka = Material::where('kod', 'MAK-001')->first();
        $mleko = Material::where('kod', 'NAB-001')->first();
        $drozdze = Material::where('kod', 'DRO-001')->first();
        $cukier = Material::where('kod', 'CUK-001')->first();
        $maslo = Material::where('kod', 'TLU-001')->first();
        $jajka = Material::where('kod', 'JAJ-001')->first();
        $sol = Material::where('kod', 'DOD-001')->first();

        // Krok 1: Przygotowanie składników - wszystkie składniki
        if (isset($steps[0])) {
            if ($maka) $steps[0]->addMaterial($maka, 0.8, 'kg', ['kolejnosc' => 0]);
            if ($maslo) $steps[0]->addMaterial($maslo, 0.15, 'kg', ['kolejnosc' => 1, 'sposob_przygotowania' => 'rozmiękczone']);
            if ($jajka) $steps[0]->addMaterial($jajka, 2, 'szt', ['kolejnosc' => 2, 'uwagi' => '1 do ciasta + 1 do smarowania']);
        }

        // Krok 2: Przygotowanie rozczyny drożdżowej - mleko, drożdże, cukier
        if (isset($steps[1])) {
            if ($mleko) $steps[1]->addMaterial($mleko, 0.25, 'l', ['kolejnosc' => 0, 'temperatura_c' => 37, 'uwagi' => 'Ciepłe mleko']);
            if ($drozdze) $steps[1]->addMaterial($drozdze, 0.02, 'kg', ['kolejnosc' => 1, 'sposob_przygotowania' => 'świeże lub suszone']);
            if ($cukier) $steps[1]->addMaterial($cukier, 0.02, 'kg', ['kolejnosc' => 2, 'uwagi' => 'Łyżka cukru do aktywacji drożdży']);
        }

        // Krok 3: Łączenie składników - pozostały cukier i sól
        if (isset($steps[2])) {
            if ($cukier) $steps[2]->addMaterial($cukier, 0.08, 'kg', ['kolejnosc' => 0, 'uwagi' => 'Pozostały cukier']);
            if ($sol) $steps[2]->addMaterial($sol, 0.008, 'kg', ['kolejnosc' => 1, 'uwagi' => 'Szczypta soli']);
        }

        // Krok 9: Smarowanie jajkiem - jajko do smarowania (już uwzględnione w kroku 1)
        // Nie dodajemy dodatkowego jajka, bo już jest uwzględnione w kroku 1
    }
}
