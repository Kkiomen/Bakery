<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            // Mąki
            [
                'kod' => 'MAK-001',
                'nazwa' => 'Mąka pszenna typ 500',
                'opis' => 'Mąka pszenna uniwersalna do wypieków',
                'typ' => 'mąka',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 25.0,
                'dostawca' => 'Młyny Polskie Sp. z o.o.',
                'stan_aktualny' => 150.0,
                'stan_minimalny' => 50.0,
                'stan_optymalny' => 200.0,
                'cena_zakupu_gr' => 280, // 2.80 zł/kg
                'stawka_vat' => '5',
                'dni_waznosci' => 365,
                'data_ostatniej_dostawy' => now()->subDays(5),
                'uwagi' => 'Przechowywać w suchym miejscu',
                'aktywny' => true,
            ],
            [
                'kod' => 'MAK-002',
                'nazwa' => 'Mąka żytnia razowa',
                'opis' => 'Mąka żytnia pełnoziarnista do chleba',
                'typ' => 'mąka',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 25.0,
                'dostawca' => 'Młyny Polskie Sp. z o.o.',
                'stan_aktualny' => 80.0,
                'stan_minimalny' => 30.0,
                'stan_optymalny' => 100.0,
                'cena_zakupu_gr' => 320, // 3.20 zł/kg
                'stawka_vat' => '5',
                'dni_waznosci' => 180,
                'data_ostatniej_dostawy' => now()->subDays(10),
                'uwagi' => 'Krótszy termin ważności',
                'aktywny' => true,
            ],

            // Cukry
            [
                'kod' => 'CUK-001',
                'nazwa' => 'Cukier biały kryształ',
                'opis' => 'Cukier biały do wypieków',
                'typ' => 'cukier',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 50.0,
                'dostawca' => 'Krajowa Spółka Cukrowa',
                'stan_aktualny' => 200.0,
                'stan_minimalny' => 75.0,
                'stan_optymalny' => 300.0,
                'cena_zakupu_gr' => 350, // 3.50 zł/kg
                'stawka_vat' => '5',
                'dni_waznosci' => 730,
                'data_ostatniej_dostawy' => now()->subDays(3),
                'uwagi' => null,
                'aktywny' => true,
            ],
            [
                'kod' => 'CUK-002',
                'nazwa' => 'Cukier puder',
                'opis' => 'Cukier puder do dekoracji',
                'typ' => 'cukier',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 25.0,
                'dostawca' => 'Krajowa Spółka Cukrowa',
                'stan_aktualny' => 15.0,
                'stan_minimalny' => 20.0, // Niski stan!
                'stan_optymalny' => 50.0,
                'cena_zakupu_gr' => 420, // 4.20 zł/kg
                'stawka_vat' => '5',
                'dni_waznosci' => 730,
                'data_ostatniej_dostawy' => now()->subDays(20),
                'uwagi' => 'Potrzebne uzupełnienie',
                'aktywny' => true,
            ],

            // Drożdże
            [
                'kod' => 'DRO-001',
                'nazwa' => 'Drożdże piekarskie świeże',
                'opis' => 'Drożdże świeże w kostkach',
                'typ' => 'drożdże',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 0.5,
                'dostawca' => 'Lesaffre Polska',
                'stan_aktualny' => 5.0,
                'stan_minimalny' => 3.0,
                'stan_optymalny' => 10.0,
                'cena_zakupu_gr' => 1200, // 12.00 zł/kg
                'stawka_vat' => '5',
                'dni_waznosci' => 14,
                'data_ostatniej_dostawy' => now()->subDays(2),
                'uwagi' => 'Przechowywać w lodówce',
                'aktywny' => true,
            ],
            [
                'kod' => 'DRO-002',
                'nazwa' => 'Drożdże suche instant',
                'opis' => 'Drożdże suche błyskawiczne',
                'typ' => 'drożdże',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 0.5,
                'dostawca' => 'Lesaffre Polska',
                'stan_aktualny' => 2.0,
                'stan_minimalny' => 1.0,
                'stan_optymalny' => 5.0,
                'cena_zakupu_gr' => 2800, // 28.00 zł/kg
                'stawka_vat' => '5',
                'dni_waznosci' => 365,
                'data_ostatniej_dostawy' => now()->subDays(15),
                'uwagi' => 'Przechowywać w szczelnym opakowaniu',
                'aktywny' => true,
            ],

            // Tłuszcze
            [
                'kod' => 'TLU-001',
                'nazwa' => 'Masło extra 82%',
                'opis' => 'Masło wysokiej jakości do wypieków',
                'typ' => 'tłuszcze',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 25.0,
                'dostawca' => 'Mlekpol',
                'stan_aktualny' => 30.0,
                'stan_minimalny' => 15.0,
                'stan_optymalny' => 50.0,
                'cena_zakupu_gr' => 1850, // 18.50 zł/kg
                'stawka_vat' => '5',
                'dni_waznosci' => 60,
                'data_ostatniej_dostawy' => now()->subDays(1),
                'uwagi' => 'Przechowywać w lodówce',
                'aktywny' => true,
            ],
            [
                'kod' => 'TLU-002',
                'nazwa' => 'Olej rzepakowy',
                'opis' => 'Olej rzepakowy do smażenia i wypieków',
                'typ' => 'tłuszcze',
                'jednostka_podstawowa' => 'l',
                'waga_opakowania' => 10.0,
                'dostawca' => 'ZT Kruszwica',
                'stan_aktualny' => 25.0,
                'stan_minimalny' => 10.0,
                'stan_optymalny' => 40.0,
                'cena_zakupu_gr' => 650, // 6.50 zł/l
                'stawka_vat' => '5',
                'dni_waznosci' => 365,
                'data_ostatniej_dostawy' => now()->subDays(7),
                'uwagi' => null,
                'aktywny' => true,
            ],

            // Nabiał
            [
                'kod' => 'NAB-001',
                'nazwa' => 'Mleko 3,2%',
                'opis' => 'Mleko pełne do wypieków',
                'typ' => 'nabiał',
                'jednostka_podstawowa' => 'l',
                'waga_opakowania' => 1.0,
                'dostawca' => 'Mlekpol',
                'stan_aktualny' => 20.0,
                'stan_minimalny' => 15.0,
                'stan_optymalny' => 50.0,
                'cena_zakupu_gr' => 280, // 2.80 zł/l
                'stawka_vat' => '5',
                'dni_waznosci' => 7,
                'data_ostatniej_dostawy' => now()->subDays(1),
                'uwagi' => 'Przechowywać w lodówce',
                'aktywny' => true,
            ],

            // Jajka
            [
                'kod' => 'JAJ-001',
                'nazwa' => 'Jajka kurze L',
                'opis' => 'Jajka świeże rozmiar L',
                'typ' => 'jajka',
                'jednostka_podstawowa' => 'szt',
                'waga_opakowania' => 30.0,
                'dostawca' => 'Ferma Drobiu Kowalski',
                'stan_aktualny' => 180.0,
                'stan_minimalny' => 60.0,
                'stan_optymalny' => 300.0,
                'cena_zakupu_gr' => 65, // 0.65 zł/szt
                'stawka_vat' => '5',
                'dni_waznosci' => 21,
                'data_ostatniej_dostawy' => now()->subDays(2),
                'uwagi' => 'Przechowywać w lodówce',
                'aktywny' => true,
            ],

            // Dodatki
            [
                'kod' => 'DOD-001',
                'nazwa' => 'Sól kuchenna',
                'opis' => 'Sól kamienna do wypieków',
                'typ' => 'dodatki',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 25.0,
                'dostawca' => 'Solino',
                'stan_aktualny' => 40.0,
                'stan_minimalny' => 10.0,
                'stan_optymalny' => 50.0,
                'cena_zakupu_gr' => 120, // 1.20 zł/kg
                'stawka_vat' => '5',
                'dni_waznosci' => null, // Nie psuje się
                'data_ostatniej_dostawy' => now()->subDays(30),
                'uwagi' => 'Przechowywać w suchym miejscu',
                'aktywny' => true,
            ],
            [
                'kod' => 'DOD-002',
                'nazwa' => 'Proszek do pieczenia',
                'opis' => 'Proszek do pieczenia Dr. Oetker',
                'typ' => 'dodatki',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 1.0,
                'dostawca' => 'Dr. Oetker',
                'stan_aktualny' => 3.0,
                'stan_minimalny' => 2.0,
                'stan_optymalny' => 8.0,
                'cena_zakupu_gr' => 2200, // 22.00 zł/kg
                'stawka_vat' => '23',
                'dni_waznosci' => 730,
                'data_ostatniej_dostawy' => now()->subDays(14),
                'uwagi' => 'Przechowywać w suchym miejscu',
                'aktywny' => true,
            ],

            // Przykład surowca bez stanu (do zamówienia)
            [
                'kod' => 'OWO-001',
                'nazwa' => 'Rodzynki sułtańskie',
                'opis' => 'Rodzynki do ciast i chleba',
                'typ' => 'owoce',
                'jednostka_podstawowa' => 'kg',
                'waga_opakowania' => 10.0,
                'dostawca' => 'Nuts & Fruits',
                'stan_aktualny' => 0.0, // Brak w magazynie!
                'stan_minimalny' => 5.0,
                'stan_optymalny' => 20.0,
                'cena_zakupu_gr' => 1800, // 18.00 zł/kg
                'stawka_vat' => '5',
                'dni_waznosci' => 180,
                'data_ostatniej_dostawy' => null,
                'uwagi' => 'Do zamówienia',
                'aktywny' => true,
            ],
        ];

        foreach ($materials as $materialData) {
            Material::create($materialData);
        }
    }
}
