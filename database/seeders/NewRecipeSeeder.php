<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Material;
use App\Models\Product;

class NewRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $this->createBulkiPszenneKlasyczne();
        // Pozostałe receptury dodamy później po sprawdzeniu pierwszej
        // $this->createChlebZytniTradycyjny();
        // $this->createRogaleSwietomarcinski();
    }

    private function createBulkiPszenneKlasyczne()
    {
        $recipe = Recipe::create([
            'kod' => 'REC-BULKA-001',
            'nazwa' => 'Bułki pszenne klasyczne',
            'opis' => 'Tradycyjne bułki pszenne na drożdżach z chrupiącą skórką i miękkkim wnętrzem. Idealne na śniadanie.',
            'kategoria' => 'pieczywo',
            'poziom_trudnosci' => 'łatwy',
            'ilosc_porcji' => 20,
            'waga_jednostkowa_g' => 60,
            'czas_przygotowania_min' => 30,
            'czas_wypiekania_min' => 15,
            'czas_calkowity_min' => 180,
            'temperatura_c' => 220,
            'instrukcje_wypiekania' => 'Piec z parą przez pierwsze 5 minut, następnie bez pary do uzyskania złotego koloru.',
            'autor' => 'Mistrz Piekarz',
            'wersja' => '1.0',
            'aktywny' => true,
            'testowany' => true,
            'wskazowki' => 'Ważne jest odpowiednie wyrastanie ciasta - powinno podwoić swoją objętość.',
            'uwagi' => 'Receptura sprawdzona wielokrotnie, zawsze udana.'
        ]);

        // PROCES 1: Przygotowanie zaczynu
        $step1 = $recipe->steps()->create([
            'kolejnosc' => 0,
            'typ' => 'przygotowanie',
            'nazwa' => 'Przygotowanie zaczynu drożdżowego',
            'opis' => 'Rozpuść drożdże w ciepłej wodzie z odrobiną cukru. Dodaj część mąki i wymieszaj do gładkiej masy. Zostaw na 15 minut do spienienia.',
            'czas_min' => 15,
            'temperatura_c' => 28,
            'narzedzia' => 'miska, łyżka',
            'wskazowki' => 'Woda nie może być za gorąca - sprawdź temperaturę nadgarstkiem',
            'kryteria_oceny' => 'Zaczyn powinien się spienić i podwoić objętość',
            'obowiazkowy' => true,
        ]);

        // Składniki do zaczynu - używaj kodów dla pewności
        $maka = Material::where('kod', 'MAK-001')->first(); // Mąka pszenna typ 500
        $drozdze = Material::where('kod', 'DRO-001')->first(); // Drożdże piekarskie świeże
        $cukier = Material::where('kod', 'CUK-001')->first(); // Cukier biały kryształ
        $mleko = Material::where('kod', 'NAB-001')->first(); // Mleko 3,2% (zamiast wody)

        if ($maka) $step1->materials()->attach($maka->id, ['ilosc' => 150, 'jednostka' => 'g', 'kolejnosc' => 0]);
        if ($mleko) $step1->materials()->attach($mleko->id, ['ilosc' => 200, 'jednostka' => 'ml', 'temperatura_c' => 37, 'sposob_przygotowania' => 'ciepłe', 'kolejnosc' => 1]);
        if ($drozdze) {
            // Dodaj drożdże z zamiennikami
            $step1->materials()->attach($drozdze->id, ['ilosc' => 25, 'jednostka' => 'g', 'kolejnosc' => 2]);

            // Dodaj zamienniki dla drożdży
            $drozdze_suche = Material::where('kod', 'DRO-002')->first(); // Drożdże suche instant
            if ($drozdze_suche) {
                $zamienniki = [
                    [
                        'material_id' => $drozdze_suche->id,
                        'material_name' => $drozdze_suche->nazwa,
                        'wspolczynnik_przeliczenia' => 0.3,
                        'uwagi' => 'Drożdże świeże → suche (1:0.3)',
                        'jednostka' => $drozdze_suche->jednostka_podstawowa,
                    ]
                ];

                $step1->materials()->updateExistingPivot($drozdze->id, [
                    'zamienniki' => json_encode($zamienniki),
                    'ma_zamienniki' => true,
                ]);
            }
        }
        if ($cukier) $step1->materials()->attach($cukier->id, ['ilosc' => 10, 'jednostka' => 'g', 'kolejnosc' => 3]);

        // PROCES 2: Pierwszy wzrost zaczynu
        $step2 = $recipe->steps()->create([
            'kolejnosc' => 1,
            'typ' => 'wyrastanie',
            'nazwa' => 'Pierwszy wzrost zaczynu',
            'opis' => 'Przykryj zaczyn ściereczką i zostaw w ciepłym miejscu do podwojenia objętości.',
            'czas_min' => 30,
            'temperatura_c' => 28,
            'wilgotnosc_proc' => 75,
            'narzedzia' => 'ściereczka, ciepłe miejsce',
            'wskazowki' => 'Można postawić nad ciepłym piekarnikiem lub w lekko nagrzanym piekarniku',
            'kryteria_oceny' => 'Zaczyn powinien być pęcherzykowy i podwojony',
            'obowiazkowy' => true,
        ]);

        // PROCES 3: Dodanie reszty składników
        $step3 = $recipe->steps()->create([
            'kolejnosc' => 2,
            'typ' => 'przygotowanie',
            'nazwa' => 'Dodanie pozostałych składników',
            'opis' => 'Do wyrośniętego zaczynu dodaj pozostałą mąkę, sól, cukier i roztopione masło. Wymieszaj wszystko razem.',
            'czas_min' => 10,
            'narzedzia' => 'duża miska, łyżka drewniana',
            'wskazowki' => 'Masło powinno być roztopione ale nie gorące',
            'obowiazkowy' => true,
        ]);

        // Składniki do głównego ciasta
        $sol = Material::where('kod', 'DOD-001')->first(); // Sól kuchenna
        $maslo = Material::where('kod', 'TLU-001')->first(); // Masło extra 82%

        if ($maka) $step3->materials()->attach($maka->id, ['ilosc' => 350, 'jednostka' => 'g', 'kolejnosc' => 0]);
        if ($sol) $step3->materials()->attach($sol->id, ['ilosc' => 8, 'jednostka' => 'g', 'kolejnosc' => 1]);
        if ($cukier) $step3->materials()->attach($cukier->id, ['ilosc' => 15, 'jednostka' => 'g', 'kolejnosc' => 2]);
        if ($maslo) {
            // Dodaj masło z zamiennikami
            $step3->materials()->attach($maslo->id, ['ilosc' => 50, 'jednostka' => 'g', 'sposob_przygotowania' => 'roztopione', 'temperatura_c' => 40, 'kolejnosc' => 3]);

            // Dodaj zamienniki dla masła
            $olej = Material::where('kod', 'TLU-002')->first(); // Olej rzepakowy
            if ($olej) {
                $zamienniki = [
                    [
                        'material_id' => $olej->id,
                        'material_name' => $olej->nazwa,
                        'wspolczynnik_przeliczenia' => 0.8,
                        'uwagi' => 'Masło → olej (1:0.8) - lżejsze ciasto',
                        'jednostka' => $olej->jednostka_podstawowa,
                    ]
                ];

                $step3->materials()->updateExistingPivot($maslo->id, [
                    'zamienniki' => json_encode($zamienniki),
                    'ma_zamienniki' => true,
                ]);
            }
        }

        // PROCES 4: Wyrabianie ciasta
        $step4 = $recipe->steps()->create([
            'kolejnosc' => 3,
            'typ' => 'wyrabianie',
            'nazwa' => 'Wyrabianie ciasta',
            'opis' => 'Wyrabiaj ciasto przez 8-10 minut aż stanie się gładkie i elastyczne. Ciasto powinno odchodzić od ścianek miski.',
            'czas_min' => 10,
            'narzedzia' => 'robot planetarny lub ręce',
            'wskazowki' => 'Ciasto jest gotowe gdy nie klei się do rąk i jest elastyczne',
            'kryteria_oceny' => 'Ciasto gładkie, elastyczne, nie klei się',
            'obowiazkowy' => true,
        ]);

        // PROCES 5: Drugi wzrost ciasta
        $step5 = $recipe->steps()->create([
            'kolejnosc' => 4,
            'typ' => 'wyrastanie',
            'nazwa' => 'Drugi wzrost ciasta',
            'opis' => 'Przełóż ciasto do natłuszczonej miski, przykryj i zostaw do podwojenia objętości.',
            'czas_min' => 60,
            'temperatura_c' => 28,
            'wilgotnosc_proc' => 75,
            'narzedzia' => 'natłuszczona miska, ściereczka',
            'wskazowki' => 'Ciasto powinno podwoić objętość - sprawdź palcem czy sprężyście wraca',
            'kryteria_oceny' => 'Ciasto podwojone, sprężyste w dotyku',
            'obowiazkowy' => true,
        ]);

        // PROCES 6: Formowanie bułek
        $step6 = $recipe->steps()->create([
            'kolejnosc' => 5,
            'typ' => 'formowanie',
            'nazwa' => 'Formowanie bułek',
            'opis' => 'Podziel ciasto na 20 równych części (po około 60g). Uformuj okrągłe bułki i ułóż na blasze wyłożonej papierem.',
            'czas_min' => 15,
            'narzedzia' => 'waga, nóż, blacha, papier do pieczenia',
            'wskazowki' => 'Każda bułka powinna ważyć około 60g dla równomiernego pieczenia',
            'kryteria_oceny' => 'Bułki równe, okrągłe, ładnie uformowane',
            'obowiazkowy' => true,
        ]);

        // PROCES 7: Trzeci wzrost (wyrastanie bułek)
        $step7 = $recipe->steps()->create([
            'kolejnosc' => 6,
            'typ' => 'wyrastanie',
            'nazwa' => 'Trzeci wzrost - wyrastanie bułek',
            'opis' => 'Przykryj bułki ściereczką i zostaw na 30-40 minut do zwiększenia objętości o połowę.',
            'czas_min' => 35,
            'temperatura_c' => 28,
            'wilgotnosc_proc' => 70,
            'narzedzia' => 'ściereczka',
            'wskazowki' => 'Bułki są gotowe gdy delikatnie naciśnięte palcem powoli wracają do kształtu',
            'kryteria_oceny' => 'Bułki powiększone, miękkie w dotyku',
            'obowiazkowy' => true,
        ]);

        // PROCES 8: Smarowanie i nacinanie (opcjonalne)
        $step8 = $recipe->steps()->create([
            'kolejnosc' => 7,
            'typ' => 'dekorowanie',
            'nazwa' => 'Smarowanie i dekorowanie',
            'opis' => 'Posmaruj bułki roztrzepanym jajkiem i posyp makiem lub sezamem według uznania.',
            'czas_min' => 5,
            'narzedzia' => 'pędzelek, miska',
            'wskazowki' => 'Smarowanie jajkiem da piękny złoty kolor',
            'obowiazkowy' => false,
        ]);

        // Składniki do dekoracji
        $jajka = Material::where('kod', 'JAJ-001')->first(); // Jajka kurze L

        if ($jajka) $step8->materials()->attach($jajka->id, ['ilosc' => 1, 'jednostka' => 'szt', 'sposob_przygotowania' => 'roztrzepane', 'opcjonalny' => true, 'kolejnosc' => 0]);
        // Sezam nie jest dostępny w MaterialSeeder - pomijamy

        // PROCES 9: Wypiekanie
        $step9 = $recipe->steps()->create([
            'kolejnosc' => 8,
            'typ' => 'wypiekanie',
            'nazwa' => 'Wypiekanie bułek',
            'opis' => 'Piecz w nagrzanym piekarniku przez 15-18 minut do złotego koloru. Pierwsze 5 minut z parą.',
            'czas_min' => 15,
            'temperatura_c' => 220,
            'narzedzia' => 'piekarnik, spray z wodą',
            'wskazowki' => 'Para na początku da chrupiącą skórkę. Bułki są gotowe gdy stuknięte od spodu brzmią głucho.',
            'kryteria_oceny' => 'Bułki złote, chrupiące, brzmią głucho',
            'obowiazkowy' => true,
        ]);

        // PROCES 10: Chłodzenie
        $step10 = $recipe->steps()->create([
            'kolejnosc' => 9,
            'typ' => 'chłodzenie',
            'nazwa' => 'Chłodzenie bułek',
            'opis' => 'Przełóż upieczone bułki na kratkę i zostaw do całkowitego ostygnięcia.',
            'czas_min' => 30,
            'temperatura_c' => 20,
            'narzedzia' => 'kratka do chłodzenia',
            'wskazowki' => 'Nie przykrywaj gorących bułek - skórka zmięknie',
            'kryteria_oceny' => 'Bułki całkowicie ostygłe, skórka chrupiąca',
            'obowiazkowy' => true,
        ]);
    }

    private function createChlebZytniTradycyjny()
    {
        $recipe = Recipe::create([
            'kod' => 'REC-CHLEB-002',
            'nazwa' => 'Chleb żytni tradycyjny',
            'opis' => 'Aromatyczny chleb żytni na żurku z nasionami słonecznika. Długo zachowuje świeżość.',
            'kategoria' => 'pieczywo',
            'poziom_trudnosci' => 'średni',
            'ilosc_porcji' => 2,
            'waga_jednostkowa_g' => 800,
            'czas_przygotowania_min' => 45,
            'czas_wypiekania_min' => 50,
            'czas_calkowity_min' => 1440, // 24 godziny z żurkiem
            'temperatura_c' => 200,
            'instrukcje_wypiekania' => 'Piec z parą przez pierwsze 15 minut, następnie zmniejszyć do 180°C.',
            'autor' => 'Tradycyjna Piekarnia',
            'wersja' => '2.1',
            'aktywny' => true,
            'testowany' => true,
            'wskazowki' => 'Żurek musi być aktywny i pachnący. Chleb najlepszy po 24 godzinach.',
            'uwagi' => 'Receptura wymaga żurku żytniego - przygotować 5 dni wcześniej.'
        ]);

        // PROCES 1: Przygotowanie zaczynu żytniego
        $step1 = $recipe->steps()->create([
            'kolejnosc' => 0,
            'typ' => 'przygotowanie',
            'nazwa' => 'Przygotowanie zaczynu żytniego',
            'opis' => 'Wymieszaj mąkę żytnią z ciepłą wodą i żurkiem. Masa powinna być gęsta jak budyń.',
            'czas_min' => 15,
            'temperatura_c' => 30,
            'narzedzia' => 'duża miska, łyżka drewniana',
            'wskazowki' => 'Żurek powinien być aktywny i pachnący kwasno',
            'kryteria_oceny' => 'Zaczyn gładki, bez grudek',
            'obowiazkowy' => true,
        ]);

        // Składniki do zaczynu
        $makaZytnia = Material::where('nazwa', 'Mąka żytnia')->first();
        $woda = Material::where('nazwa', 'Woda')->first();
        $zurek = Material::where('nazwa', 'Żurek żytni')->first();

        if ($makaZytnia) $step1->materials()->attach($makaZytnia->id, ['ilosc' => 200, 'jednostka' => 'g', 'kolejnosc' => 0]);
        if ($woda) $step1->materials()->attach($woda->id, ['ilosc' => 180, 'jednostka' => 'ml', 'temperatura_c' => 35, 'sposob_przygotowania' => 'ciepła', 'kolejnosc' => 1]);
        if ($zurek) $step1->materials()->attach($zurek->id, ['ilosc' => 100, 'jednostka' => 'ml', 'sposob_przygotowania' => 'aktywny', 'kolejnosc' => 2]);

        // PROCES 2: Fermentacja zaczynu
        $step2 = $recipe->steps()->create([
            'kolejnosc' => 1,
            'typ' => 'wyrastanie',
            'nazwa' => 'Fermentacja zaczynu żytniego',
            'opis' => 'Przykryj zaczyn folią i zostaw na 12-16 godzin w temperaturze pokojowej do sfermentowania.',
            'czas_min' => 840, // 14 godzin
            'temperatura_c' => 22,
            'wilgotnosc_proc' => 80,
            'narzedzia' => 'folia spożywcza',
            'wskazowki' => 'Zaczyn powinien się spienić i nabrać kwasnego aromatu',
            'kryteria_oceny' => 'Zaczyn pęcherzykowy, kwasny zapach',
            'obowiazkowy' => true,
        ]);

        // PROCES 3: Przygotowanie głównego ciasta
        $step3 = $recipe->steps()->create([
            'kolejnosc' => 2,
            'typ' => 'mieszanie',
            'nazwa' => 'Przygotowanie głównego ciasta',
            'opis' => 'Do sfermentowanego zaczynu dodaj mąkę pszenną, sól, miód i nasiona słonecznika. Wymieszaj do połączenia.',
            'czas_min' => 10,
            'narzedzia' => 'duża miska, łyżka drewniana',
            'wskazowki' => 'Ciasto żytnie nie wymaga długiego wyrabiania',
            'obowiazkowy' => true,
        ]);

        // Składniki do głównego ciasta
        $makaPszenna = Material::where('nazwa', 'Mąka pszenna typ 500')->first();
        $sol = Material::where('nazwa', 'Sól kamienna')->first();
        $miod = Material::where('nazwa', 'Miód')->first();
        $nasionaSlonecznika = Material::where('nazwa', 'Nasiona słonecznika')->first();

        if ($makaPszenna) $step3->materials()->attach($makaPszenna->id, ['ilosc' => 300, 'jednostka' => 'g', 'kolejnosc' => 0]);
        if ($sol) $step3->materials()->attach($sol->id, ['ilosc' => 12, 'jednostka' => 'g', 'kolejnosc' => 1]);
        if ($miod) $step3->materials()->attach($miod->id, ['ilosc' => 30, 'jednostka' => 'g', 'sposob_przygotowania' => 'płynny', 'kolejnosc' => 2]);
        if ($nasionaSlonecznika) $step3->materials()->attach($nasionaSlonecznika->id, ['ilosc' => 50, 'jednostka' => 'g', 'opcjonalny' => true, 'kolejnosc' => 3]);

        // PROCES 4: Formowanie bochenków
        $step4 = $recipe->steps()->create([
            'kolejnosc' => 3,
            'typ' => 'formowanie',
            'nazwa' => 'Formowanie bochenków',
            'opis' => 'Podziel ciasto na 2 części i uformuj podłużne bochenki. Ułóż w foremkach lub na blasze.',
            'czas_min' => 10,
            'narzedzia' => 'foremki do chleba lub blacha',
            'wskazowki' => 'Ciasto żytnie jest lepkie - można lekko podsypać mąką',
            'kryteria_oceny' => 'Bochenki równe, ładnie uformowane',
            'obowiazkowy' => true,
        ]);

        // PROCES 5: Ostatni wzrost
        $step5 = $recipe->steps()->create([
            'kolejnosc' => 4,
            'typ' => 'wyrastanie',
            'nazwa' => 'Ostatni wzrost chleba',
            'opis' => 'Przykryj bochenki i zostaw na 2-3 godziny do zwiększenia objętości o 1/3.',
            'czas_min' => 150,
            'temperatura_c' => 25,
            'wilgotnosc_proc' => 75,
            'narzedzia' => 'ściereczka',
            'wskazowki' => 'Chleb żytni rośnie wolniej niż pszenny',
            'kryteria_oceny' => 'Bochenki powiększone, sprężyste',
            'obowiazkowy' => true,
        ]);

        // PROCES 6: Nacinanie i dekorowanie
        $step6 = $recipe->steps()->create([
            'kolejnosc' => 5,
            'typ' => 'dekorowanie',
            'nazwa' => 'Nacinanie i dekorowanie',
            'opis' => 'Natnij bochenki ukośnie na głębokość 1cm. Posmaruj wodą i posyp nasionami.',
            'czas_min' => 5,
            'narzedzia' => 'ostry nóż, pędzelek',
            'wskazowki' => 'Nacięcia powinny być głębokie i równe',
            'obowiazkowy' => true,
        ]);

        if ($nasionaSlonecznika) $step6->materials()->attach($nasionaSlonecznika->id, ['ilosc' => 20, 'jednostka' => 'g', 'opcjonalny' => true, 'kolejnosc' => 0]);

        // PROCES 7: Wypiekanie
        $step7 = $recipe->steps()->create([
            'kolejnosc' => 6,
            'typ' => 'wypiekanie',
            'nazwa' => 'Wypiekanie chleba',
            'opis' => 'Piecz 50-60 minut. Pierwsze 15 minut z parą w 200°C, potem 180°C bez pary.',
            'czas_min' => 55,
            'temperatura_c' => 200,
            'narzedzia' => 'piekarnik, spray z wodą',
            'wskazowki' => 'Chleb gotowy gdy stuknięty brzmi głucho i ma temperaturę wewnętrzną 95°C',
            'kryteria_oceny' => 'Chleb ciemnobrązowy, brzmi głucho',
            'obowiazkowy' => true,
        ]);

        // PROCES 8: Chłodzenie
        $step8 = $recipe->steps()->create([
            'kolejnosc' => 7,
            'typ' => 'chłodzenie',
            'nazwa' => 'Chłodzenie chleba',
            'opis' => 'Wyjmij z foremek i chłódź na kratce minimum 2 godziny przed krojeniem.',
            'czas_min' => 120,
            'temperatura_c' => 20,
            'narzedzia' => 'kratka do chłodzenia',
            'wskazowki' => 'Chleb żytni najlepszy po 24 godzinach - skórka zmięknie',
            'kryteria_oceny' => 'Chleb całkowicie ostygły',
            'obowiazkowy' => true,
        ]);
    }

    private function createRogaleSwietomarcinski()
    {
        $recipe = Recipe::create([
            'kod' => 'REC-ROGAL-003',
            'nazwa' => 'Rogale świętomarcińskie',
            'opis' => 'Tradycyjne poznańskie rogale z białym makiem, migdałami i rodzynkami. Specjalność na Święty Marcin.',
            'kategoria' => 'ciasta',
            'poziom_trudnosci' => 'trudny',
            'ilosc_porcji' => 8,
            'waga_jednostkowa_g' => 200,
            'czas_przygotowania_min' => 120,
            'czas_wypiekania_min' => 25,
            'czas_calkowity_min' => 300,
            'temperatura_c' => 180,
            'instrukcje_wypiekania' => 'Piec w średniej temperaturze do złotego koloru, nie przesuszać.',
            'autor' => 'Poznańska Tradycja',
            'wersja' => '1.5',
            'aktywny' => true,
            'testowany' => true,
            'wskazowki' => 'Masa makowa musi być gęsta. Ciasto powinno być elastyczne i nie kleić się.',
            'uwagi' => 'Receptura chroniona - tradycyjna poznańska. Najlepsze po 1 dniu.'
        ]);

        // PROCES 1: Przygotowanie ciasta drożdżowego
        $step1 = $recipe->steps()->create([
            'kolejnosc' => 0,
            'typ' => 'przygotowanie',
            'nazwa' => 'Przygotowanie ciasta drożdżowego',
            'opis' => 'Rozpuść drożdże w ciepłym mleku z cukrem. Dodaj mąkę, jajka, masło i sól. Wymieszaj.',
            'czas_min' => 20,
            'temperatura_c' => 25,
            'narzedzia' => 'duża miska, mikser',
            'wskazowki' => 'Mleko nie może być za gorące - sprawdź temperaturę',
            'kryteria_oceny' => 'Ciasto gładkie, elastyczne',
            'obowiazkowy' => true,
        ]);

        // Składniki do ciasta
        $maka = Material::where('nazwa', 'Mąka pszenna typ 500')->first();
        $mleko = Material::where('nazwa', 'Mleko')->first();
        $drozdze = Material::where('nazwa', 'Drożdże piekarskie świeże')->first();
        $cukier = Material::where('nazwa', 'Cukier biały')->first();
        $jajka = Material::where('nazwa', 'Jajka kurze')->first();
        $maslo = Material::where('nazwa', 'Masło')->first();
        $sol = Material::where('nazwa', 'Sól kamienna')->first();

        if ($maka) $step1->materials()->attach($maka->id, ['ilosc' => 500, 'jednostka' => 'g', 'kolejnosc' => 0]);
        if ($mleko) $step1->materials()->attach($mleko->id, ['ilosc' => 200, 'jednostka' => 'ml', 'temperatura_c' => 37, 'sposob_przygotowania' => 'ciepłe', 'kolejnosc' => 1]);
        if ($drozdze) $step1->materials()->attach($drozdze->id, ['ilosc' => 30, 'jednostka' => 'g', 'kolejnosc' => 2]);
        if ($cukier) $step1->materials()->attach($cukier->id, ['ilosc' => 80, 'jednostka' => 'g', 'kolejnosc' => 3]);
        if ($jajka) $step1->materials()->attach($jajka->id, ['ilosc' => 2, 'jednostka' => 'szt', 'sposob_przygotowania' => 'w temperaturze pokojowej', 'kolejnosc' => 4]);
        if ($maslo) $step1->materials()->attach($maslo->id, ['ilosc' => 100, 'jednostka' => 'g', 'sposob_przygotowania' => 'miękkie', 'kolejnosc' => 5]);
        if ($sol) $step1->materials()->attach($sol->id, ['ilosc' => 5, 'jednostka' => 'g', 'kolejnosc' => 6]);

        // PROCES 2: Wyrabianie ciasta
        $step2 = $recipe->steps()->create([
            'kolejnosc' => 1,
            'typ' => 'wyrabianie',
            'nazwa' => 'Wyrabianie ciasta na rogale',
            'opis' => 'Wyrabiaj ciasto przez 15 minut do uzyskania gładkiej, elastycznej konsystencji.',
            'czas_min' => 15,
            'narzedzia' => 'robot planetarny lub ręce',
            'wskazowki' => 'Ciasto powinno odchodzić od ścianek miski',
            'kryteria_oceny' => 'Ciasto elastyczne, nie klei się',
            'obowiazkowy' => true,
        ]);

        // PROCES 3: Pierwszy wzrost ciasta
        $step3 = $recipe->steps()->create([
            'kolejnosc' => 2,
            'typ' => 'wyrastanie',
            'nazwa' => 'Pierwszy wzrost ciasta',
            'opis' => 'Przełóż ciasto do natłuszczonej miski, przykryj i zostaw na 1,5 godziny.',
            'czas_min' => 90,
            'temperatura_c' => 28,
            'wilgotnosc_proc' => 75,
            'narzedzia' => 'natłuszczona miska, ściereczka',
            'wskazowki' => 'Ciasto powinno podwoić objętość',
            'kryteria_oceny' => 'Ciasto podwojone, pęcherzykowe',
            'obowiazkowy' => true,
        ]);

        // PROCES 4: Przygotowanie masy makowej
        $step4 = $recipe->steps()->create([
            'kolejnosc' => 3,
            'typ' => 'przygotowanie',
            'nazwa' => 'Przygotowanie masy makowej',
            'opis' => 'Zmiel mak, dodaj cukier, masło, migdały, rodzynki i rum. Wymieszaj do gęstej masy.',
            'czas_min' => 30,
            'narzedzia' => 'młynek do maku, miska',
            'wskazowki' => 'Masa powinna być gęsta, nie płynna',
            'kryteria_oceny' => 'Masa jednorodna, gęsta',
            'obowiazkowy' => true,
        ]);

        // Składniki do masy makowej
        $mak = Material::where('nazwa', 'Mak')->first();
        $migdaly = Material::where('nazwa', 'Migdały')->first();
        $rodzynki = Material::where('nazwa', 'Rodzynki')->first();

        if ($mak) $step4->materials()->attach($mak->id, ['ilosc' => 300, 'jednostka' => 'g', 'sposob_przygotowania' => 'zmielony', 'kolejnosc' => 0]);
        if ($cukier) $step4->materials()->attach($cukier->id, ['ilosc' => 150, 'jednostka' => 'g', 'kolejnosc' => 1]);
        if ($maslo) $step4->materials()->attach($maslo->id, ['ilosc' => 80, 'jednostka' => 'g', 'sposob_przygotowania' => 'roztopione', 'kolejnosc' => 2]);
        if ($migdaly) $step4->materials()->attach($migdaly->id, ['ilosc' => 100, 'jednostka' => 'g', 'sposob_przygotowania' => 'posiekane', 'kolejnosc' => 3]);
        if ($rodzynki) $step4->materials()->attach($rodzynki->id, ['ilosc' => 80, 'jednostka' => 'g', 'sposob_przygotowania' => 'namoczone w rumie', 'kolejnosc' => 4]);

        // PROCES 5: Formowanie rogali
        $step5 = $recipe->steps()->create([
            'kolejnosc' => 4,
            'typ' => 'formowanie',
            'nazwa' => 'Formowanie rogali',
            'opis' => 'Podziel ciasto na 8 części. Rozwałkuj, nałóż masę makową i uformuj rogale.',
            'czas_min' => 45,
            'narzedzia' => 'wałek, nóż, blacha',
            'wskazowki' => 'Rogale powinny być szczelnie zawinięte, żeby masa nie wyciekała',
            'kryteria_oceny' => 'Rogale równe, szczelnie zawinięte',
            'obowiazkowy' => true,
        ]);

        // PROCES 6: Drugi wzrost rogali
        $step6 = $recipe->steps()->create([
            'kolejnosc' => 5,
            'typ' => 'wyrastanie',
            'nazwa' => 'Drugi wzrost rogali',
            'opis' => 'Ułóż rogale na blasze, przykryj i zostaw na 45 minut do zwiększenia objętości.',
            'czas_min' => 45,
            'temperatura_c' => 28,
            'wilgotnosc_proc' => 70,
            'narzedzia' => 'blacha, ściereczka',
            'wskazowki' => 'Rogale powinny być miękkie i powiększone',
            'kryteria_oceny' => 'Rogale powiększone, miękkie',
            'obowiazkowy' => true,
        ]);

        // PROCES 7: Smarowanie przed pieczeniem
        $step7 = $recipe->steps()->create([
            'kolejnosc' => 6,
            'typ' => 'dekorowanie',
            'nazwa' => 'Smarowanie przed pieczeniem',
            'opis' => 'Posmaruj rogale roztrzepanym jajkiem dla pięknego złotego koloru.',
            'czas_min' => 5,
            'narzedzia' => 'pędzelek, miska',
            'wskazowki' => 'Smaruj delikatnie, żeby nie uszkodzić rogali',
            'obowiazkowy' => true,
        ]);

        if ($jajka) $step7->materials()->attach($jajka->id, ['ilosc' => 1, 'jednostka' => 'szt', 'sposob_przygotowania' => 'roztrzepane', 'kolejnosc' => 0]);

        // PROCES 8: Wypiekanie rogali
        $step8 = $recipe->steps()->create([
            'kolejnosc' => 7,
            'typ' => 'wypiekanie',
            'nazwa' => 'Wypiekanie rogali świętomarcińskich',
            'opis' => 'Piecz 20-25 minut w 180°C do złotego koloru. Nie przesuszać.',
            'czas_min' => 25,
            'temperatura_c' => 180,
            'narzedzia' => 'piekarnik',
            'wskazowki' => 'Rogale są gotowe gdy są złote i lekko sprężyste',
            'kryteria_oceny' => 'Rogale złote, nie przesuszone',
            'obowiazkowy' => true,
        ]);

        // PROCES 9: Lukrowanie (opcjonalne)
        $step9 = $recipe->steps()->create([
            'kolejnosc' => 8,
            'typ' => 'dekorowanie',
            'nazwa' => 'Lukrowanie rogali',
            'opis' => 'Przygotuj lukier z cukru pudru i mleka. Polukruj ostygłe rogale.',
            'czas_min' => 15,
            'narzedzia' => 'miska, łyżka',
            'wskazowki' => 'Lukier nakładaj na całkowicie ostygłe rogale',
            'obowiazkowy' => false,
        ]);

        $cukierPuder = Material::where('nazwa', 'Cukier puder')->first();
        if ($cukierPuder) $step9->materials()->attach($cukierPuder->id, ['ilosc' => 100, 'jednostka' => 'g', 'opcjonalny' => true, 'kolejnosc' => 0]);
        if ($mleko) $step9->materials()->attach($mleko->id, ['ilosc' => 30, 'jednostka' => 'ml', 'opcjonalny' => true, 'kolejnosc' => 1]);
    }
}
