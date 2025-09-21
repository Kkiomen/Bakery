<?php

namespace Database\Seeders;

use App\Models\Contractor;
use Illuminate\Database\Seeder;

class ContractorSeeder extends Seeder
{
    public function run(): void
    {
        $contractors = [
            [
                'nazwa' => 'Piekarnia "Złoty Kłos"',
                'nip' => '1234567890',
                'regon' => '123456789',
                'adres' => 'ul. Piekarska 15',
                'kod_pocztowy' => '00-001',
                'miasto' => 'Warszawa',
                'telefon' => '22 123 45 67',
                'email' => 'kontakt@zlotyklos.pl',
                'osoba_kontaktowa' => 'Jan Kowalski',
                'telefon_kontaktowy' => '22 123 45 68',
                'typ' => 'klient',
                'aktywny' => true,
                'uwagi' => 'Stały klient, zamawia codziennie rano',
            ],
            [
                'nazwa' => 'Restauracja "Smakowitka"',
                'nip' => '9876543210',
                'adres' => 'ul. Restauracyjna 8',
                'kod_pocztowy' => '00-002',
                'miasto' => 'Warszawa',
                'telefon' => '22 987 65 43',
                'email' => 'zamowienia@smakowitka.pl',
                'osoba_kontaktowa' => 'Anna Nowak',
                'telefon_kontaktowy' => '22 987 65 44',
                'typ' => 'klient',
                'aktywny' => true,
                'uwagi' => 'Zamawia specjalne pieczywo na weekendy',
            ],
            [
                'nazwa' => 'Młyn "Tradycja" Sp. z o.o.',
                'nip' => '5555666677',
                'regon' => '555666777',
                'adres' => 'ul. Młyńska 25',
                'kod_pocztowy' => '05-100',
                'miasto' => 'Nowy Dwór Mazowiecki',
                'telefon' => '22 555 66 77',
                'email' => 'biuro@mlyn-tradycja.pl',
                'osoba_kontaktowa' => 'Marek Młynarz',
                'telefon_kontaktowy' => '22 555 66 78',
                'typ' => 'dostawca',
                'aktywny' => true,
                'uwagi' => 'Główny dostawca mąki, dostawa we wtorki i piątki',
            ],
            [
                'nazwa' => 'Hotel "Grand Palace"',
                'nip' => '1111222233',
                'adres' => 'ul. Hotelowa 1',
                'kod_pocztowy' => '00-003',
                'miasto' => 'Warszawa',
                'telefon' => '22 111 22 33',
                'email' => 'catering@grandpalace.pl',
                'osoba_kontaktowa' => 'Katarzyna Szef',
                'telefon_kontaktowy' => '22 111 22 34',
                'typ' => 'klient',
                'aktywny' => true,
                'uwagi' => 'Duże zamówienia na śniadania hotelowe',
            ],
            [
                'nazwa' => 'Sklep "U Piekarza"',
                'adres' => 'ul. Handlowa 12',
                'kod_pocztowy' => '02-456',
                'miasto' => 'Warszawa',
                'telefon' => '22 444 55 66',
                'osoba_kontaktowa' => 'Piotr Handel',
                'telefon_kontaktowy' => '22 444 55 67',
                'typ' => 'klient',
                'aktywny' => true,
                'uwagi' => 'Mały sklep, zamawia codziennie małe ilości',
            ],
            [
                'nazwa' => 'Firma Logistyczna "Szybka Dostawa"',
                'nip' => '9999888877',
                'adres' => 'ul. Logistyczna 99',
                'kod_pocztowy' => '03-789',
                'miasto' => 'Warszawa',
                'telefon' => '22 999 88 77',
                'email' => 'kontakt@szybkadostawa.pl',
                'osoba_kontaktowa' => 'Michał Kierowca',
                'telefon_kontaktowy' => '22 999 88 78',
                'typ' => 'dostawca',
                'aktywny' => false,
                'uwagi' => 'Były partner logistyczny, obecnie nieaktywny',
            ],
            [
                'nazwa' => 'Kawiarnia "Aromat"',
                'adres' => 'ul. Kawowa 5',
                'kod_pocztowy' => '01-234',
                'miasto' => 'Warszawa',
                'telefon' => '22 333 44 55',
                'email' => 'zamowienia@aromat.pl',
                'osoba_kontaktowa' => 'Ewa Barista',
                'typ' => 'klient',
                'aktywny' => true,
                'uwagi' => 'Zamawia croissanty i słodkie pieczywo',
            ],
            [
                'nazwa' => 'Hurtownia Spożywcza "Mega Hurt"',
                'nip' => '7777888899',
                'regon' => '777888999',
                'adres' => 'ul. Hurtowa 50',
                'kod_pocztowy' => '04-567',
                'miasto' => 'Warszawa',
                'telefon' => '22 777 88 99',
                'email' => 'hurt@megahurt.pl',
                'osoba_kontaktowa' => 'Robert Hurtownik',
                'telefon_kontaktowy' => '22 777 88 90',
                'typ' => 'obydwa',
                'aktywny' => true,
                'uwagi' => 'Duży partner - zarówno klient jak i dostawca składników',
            ]
        ];

        foreach ($contractors as $contractor) {
            Contractor::create($contractor);
        }
    }
}
