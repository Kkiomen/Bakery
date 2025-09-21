<?php

namespace Database\Seeders;

use App\Models\B2BClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class B2BClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'company_name' => 'Hotel Grand Palace',
                'nip' => '1234567890',
                'regon' => '123456789',
                'email' => 'zamowienia@grandpalace.pl',
                'password' => Hash::make('password123'),
                'address' => 'ul. Hotelowa 1',
                'postal_code' => '00-001',
                'city' => 'Warszawa',
                'phone' => '22 111 22 33',
                'website' => 'https://grandpalace.pl',
                'contact_person' => 'Anna Kowalska',
                'contact_phone' => '22 111 22 34',
                'contact_email' => 'anna.kowalska@grandpalace.pl',
                'business_type' => 'hotel',
                'business_description' => 'Luksusowy hotel 5-gwiazdkowy w centrum Warszawy',
                'delivery_addresses' => [
                    [
                        'name' => 'Główny hotel',
                        'address' => 'ul. Hotelowa 1, 00-001 Warszawa',
                        'contact' => 'Anna Kowalska',
                        'phone' => '22 111 22 34'
                    ]
                ],
                'preferred_delivery_time' => 'morning',
                'delivery_days' => ['monday', 'wednesday', 'friday'],
                'status' => 'active',
                'pricing_tier' => 'gold',
                'credit_limit' => 50000.00,
                'contract_start_date' => '2024-01-01',
            ],
            [
                'company_name' => 'Restauracja Smakowitka',
                'nip' => '9876543210',
                'email' => 'zamowienia@smakowitka.pl',
                'password' => Hash::make('password123'),
                'address' => 'ul. Restauracyjna 8',
                'postal_code' => '00-002',
                'city' => 'Warszawa',
                'phone' => '22 987 65 43',
                'contact_person' => 'Piotr Nowak',
                'contact_phone' => '22 987 65 44',
                'contact_email' => 'piotr.nowak@smakowitka.pl',
                'business_type' => 'restaurant',
                'business_description' => 'Restauracja serwująca tradycyjną polską kuchnię',
                'delivery_addresses' => [
                    [
                        'name' => 'Restauracja główna',
                        'address' => 'ul. Restauracyjna 8, 00-002 Warszawa',
                        'contact' => 'Piotr Nowak',
                        'phone' => '22 987 65 44'
                    ]
                ],
                'preferred_delivery_time' => 'morning',
                'delivery_days' => ['tuesday', 'thursday', 'saturday'],
                'status' => 'active',
                'pricing_tier' => 'silver',
                'credit_limit' => 25000.00,
                'contract_start_date' => '2024-02-01',
            ],
            [
                'company_name' => 'Kawiarnia Aromat',
                'nip' => '5555666677',
                'email' => 'zamowienia@aromat.pl',
                'password' => Hash::make('password123'),
                'address' => 'ul. Kawowa 5',
                'postal_code' => '01-234',
                'city' => 'Warszawa',
                'phone' => '22 333 44 55',
                'contact_person' => 'Ewa Barista',
                'contact_phone' => '22 333 44 56',
                'contact_email' => 'ewa.barista@aromat.pl',
                'business_type' => 'cafe',
                'business_description' => 'Przytulna kawiarnia z autorskimi wypiekami',
                'delivery_addresses' => [
                    [
                        'name' => 'Kawiarnia',
                        'address' => 'ul. Kawowa 5, 01-234 Warszawa',
                        'contact' => 'Ewa Barista',
                        'phone' => '22 333 44 56'
                    ]
                ],
                'preferred_delivery_time' => 'morning',
                'delivery_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'status' => 'active',
                'pricing_tier' => 'bronze',
                'credit_limit' => 15000.00,
                'contract_start_date' => '2024-03-01',
            ],
            [
                'company_name' => 'Sklep Pieczywo i Słodycze',
                'nip' => '7777888899',
                'email' => 'zamowienia@pieczywo-sklep.pl',
                'password' => Hash::make('password123'),
                'address' => 'ul. Handlowa 12',
                'postal_code' => '02-456',
                'city' => 'Warszawa',
                'phone' => '22 444 55 66',
                'contact_person' => 'Marek Handlowiec',
                'contact_phone' => '22 444 55 67',
                'contact_email' => 'marek@pieczywo-sklep.pl',
                'business_type' => 'shop',
                'business_description' => 'Sklep detaliczny z pieczywem i słodyczami',
                'delivery_addresses' => [
                    [
                        'name' => 'Sklep główny',
                        'address' => 'ul. Handlowa 12, 02-456 Warszawa',
                        'contact' => 'Marek Handlowiec',
                        'phone' => '22 444 55 67'
                    ]
                ],
                'preferred_delivery_time' => 'morning',
                'delivery_days' => ['monday', 'wednesday', 'friday'],
                'status' => 'active',
                'pricing_tier' => 'standard',
                'credit_limit' => 10000.00,
                'contract_start_date' => '2024-04-01',
            ],
            [
                'company_name' => 'Catering Premium',
                'nip' => '1111222233',
                'email' => 'zamowienia@catering-premium.pl',
                'password' => Hash::make('password123'),
                'address' => 'ul. Cateringowa 99',
                'postal_code' => '03-789',
                'city' => 'Warszawa',
                'phone' => '22 999 88 77',
                'website' => 'https://catering-premium.pl',
                'contact_person' => 'Katarzyna Szef',
                'contact_phone' => '22 999 88 78',
                'contact_email' => 'katarzyna@catering-premium.pl',
                'business_type' => 'catering',
                'business_description' => 'Catering na eventy biznesowe i prywatne',
                'delivery_addresses' => [
                    [
                        'name' => 'Baza główna',
                        'address' => 'ul. Cateringowa 99, 03-789 Warszawa',
                        'contact' => 'Katarzyna Szef',
                        'phone' => '22 999 88 78'
                    ],
                    [
                        'name' => 'Magazyn',
                        'address' => 'ul. Magazynowa 15, 04-123 Warszawa',
                        'contact' => 'Jan Kierownik',
                        'phone' => '22 999 88 79'
                    ]
                ],
                'preferred_delivery_time' => 'flexible',
                'delivery_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'status' => 'active',
                'pricing_tier' => 'platinum',
                'credit_limit' => 75000.00,
                'contract_start_date' => '2023-12-01',
            ]
        ];

        foreach ($clients as $clientData) {
            B2BClient::create($clientData);
        }
    }
}
