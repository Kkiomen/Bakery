<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\ProductionOrder;
use App\Models\User;
use Carbon\Carbon;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a driver user
        $driver = User::firstOrCreate([
            'email' => 'kierowca@piekarnia.pl'
        ], [
            'name' => 'Jan Kowalski',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Get production orders to create deliveries from
        $productionOrders = ProductionOrder::with('items.product')->limit(5)->get();

        if ($productionOrders->isEmpty()) {
            $this->command->warn('Brak zleceń produkcyjnych. Najpierw uruchom ProductionOrderSeeder.');
            return;
        }

        $addresses = [
            [
                'klient_nazwa' => 'Piekarnia "Pod Starym Dębem"',
                'klient_adres' => 'ul. Marszałkowska 100',
                'kod_pocztowy' => '00-026',
                'miasto' => 'Warszawa',
                'klient_telefon' => '+48 22 123 45 67',
                'osoba_kontaktowa' => 'Anna Nowak',
                'telefon_kontaktowy' => '+48 501 234 567',
                'latitude' => 52.2319,
                'longitude' => 21.0067,
            ],
            [
                'klient_nazwa' => 'Restauracja "Smak Tradycji"',
                'klient_adres' => 'ul. Nowy Świat 25',
                'kod_pocztowy' => '00-029',
                'miasto' => 'Warszawa',
                'klient_telefon' => '+48 22 234 56 78',
                'osoba_kontaktowa' => 'Piotr Wiśniewski',
                'telefon_kontaktowy' => '+48 502 345 678',
                'latitude' => 52.2396,
                'longitude' => 21.0129,
            ],
            [
                'klient_nazwa' => 'Hotel "Grand Palace"',
                'klient_adres' => 'ul. Krakowskie Przedmieście 15',
                'kod_pocztowy' => '00-071',
                'miasto' => 'Warszawa',
                'klient_telefon' => '+48 22 345 67 89',
                'osoba_kontaktowa' => 'Maria Kowalczyk',
                'telefon_kontaktowy' => '+48 503 456 789',
                'latitude' => 52.2473,
                'longitude' => 21.0144,
            ],
            [
                'klient_nazwa' => 'Sklep "Świeże Pieczywo"',
                'klient_adres' => 'ul. Żurawia 47',
                'kod_pocztowy' => '00-680',
                'miasto' => 'Warszawa',
                'klient_telefon' => '+48 22 456 78 90',
                'osoba_kontaktowa' => 'Tomasz Lewandowski',
                'telefon_kontaktowy' => '+48 504 567 890',
                'latitude' => 52.2254,
                'longitude' => 21.0063,
            ],
            [
                'klient_nazwa' => 'Kawiarnia "Aromat"',
                'klient_adres' => 'ul. Chmielna 13',
                'kod_pocztowy' => '00-021',
                'miasto' => 'Warszawa',
                'klient_telefon' => '+48 22 567 89 01',
                'osoba_kontaktowa' => 'Katarzyna Dąbrowska',
                'telefon_kontaktowy' => '+48 505 678 901',
                'latitude' => 52.2283,
                'longitude' => 21.0103,
            ],
        ];

        $statuses = ['oczekujaca', 'przypisana', 'w_drodze', 'dostarczona'];
        $priorities = ['niski', 'normalny', 'wysoki', 'pilny'];

        foreach ($productionOrders as $index => $productionOrder) {
            $addressData = $addresses[$index] ?? $addresses[0];
            $status = $statuses[array_rand($statuses)];
            $priority = $priorities[array_rand($priorities)];

            // Create delivery
            $delivery = Delivery::create([
                'production_order_id' => $productionOrder->id,
                'driver_id' => in_array($status, ['przypisana', 'w_drodze', 'dostarczona']) ? $driver->id : null,
                'status' => $status,
                'priorytet' => $priority,
                'data_dostawy' => now()->addDays(rand(0, 7)),
                'godzina_planowana' => now()->addDays(rand(0, 7))->setTime(rand(8, 16), [0, 30][rand(0, 1)]),
                'klient_nazwa' => $addressData['klient_nazwa'],
                'klient_adres' => $addressData['klient_adres'],
                'klient_telefon' => $addressData['klient_telefon'],
                'osoba_kontaktowa' => $addressData['osoba_kontaktowa'],
                'telefon_kontaktowy' => $addressData['telefon_kontaktowy'],
                'kod_pocztowy' => $addressData['kod_pocztowy'],
                'miasto' => $addressData['miasto'],
                'latitude' => $addressData['latitude'],
                'longitude' => $addressData['longitude'],
                'kolejnosc_dostawy' => $index + 1,
                'uwagi_dostawy' => $this->getRandomDeliveryNote(),
                'godzina_rozpoczecia' => in_array($status, ['w_drodze', 'dostarczona']) ?
                    now()->subHours(rand(1, 4)) : null,
                'godzina_zakonczenia' => $status === 'dostarczona' ?
                    now()->subHours(rand(0, 2)) : null,
            ]);

            // Create delivery items from production order items
            foreach ($productionOrder->items->take(rand(1, 3)) as $orderItem) {
                $deliveryQuantity = rand(1, $orderItem->ilosc_wyprodukowana);
                $deliveredQuantity = $status === 'dostarczona' ? $deliveryQuantity :
                    ($status === 'w_drodze' ? rand(0, $deliveryQuantity) : 0);

                DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'product_id' => $orderItem->product_id,
                    'production_order_item_id' => $orderItem->id,
                    'nazwa_produktu' => $orderItem->product->nazwa,
                    'ilosc' => $deliveryQuantity,
                    'jednostka' => $orderItem->jednostka,
                    'ilosc_dostarczona' => $deliveredQuantity,
                    'waga_kg' => $orderItem->product->waga_g ?
                        ($orderItem->product->waga_g * $deliveryQuantity) / 1000 : null,
                    'status' => $this->getItemStatus($deliveredQuantity, $deliveryQuantity),
                ]);
            }

            $this->command->info("Utworzono dostawę: {$delivery->numer_dostawy} dla {$delivery->klient_nazwa}");
        }

        // Create some additional deliveries for today and tomorrow
        $this->createTodayTomorrowDeliveries($driver, $addresses);
    }

    private function createTodayTomorrowDeliveries($driver, $addresses)
    {
        $productionOrders = ProductionOrder::with('items.product')
            ->where('status', 'zakonczone')
            ->skip(5)
            ->limit(6)
            ->get();

        if ($productionOrders->isEmpty()) {
            return;
        }

        $dates = [now(), now()->addDay()];
        $statuses = ['przypisana', 'w_drodze', 'oczekujaca'];

        foreach ($dates as $dateIndex => $date) {
            foreach ($productionOrders->take(3) as $index => $productionOrder) {
                $addressData = $addresses[($dateIndex * 3 + $index) % count($addresses)];
                $status = $statuses[array_rand($statuses)];

                $delivery = Delivery::create([
                    'production_order_id' => $productionOrder->id,
                    'driver_id' => $status !== 'oczekujaca' ? $driver->id : null,
                    'status' => $status,
                    'priorytet' => ['normalny', 'wysoki', 'pilny'][rand(0, 2)],
                    'data_dostawy' => $date->format('Y-m-d'),
                    'godzina_planowana' => $date->copy()->setTime(rand(9, 15), [0, 30][rand(0, 1)]),
                    'klient_nazwa' => $addressData['klient_nazwa'],
                    'klient_adres' => $addressData['klient_adres'],
                    'klient_telefon' => $addressData['klient_telefon'],
                    'osoba_kontaktowa' => $addressData['osoba_kontaktowa'],
                    'telefon_kontaktowy' => $addressData['telefon_kontaktowy'],
                    'kod_pocztowy' => $addressData['kod_pocztowy'],
                    'miasto' => $addressData['miasto'],
                    'latitude' => $addressData['latitude'],
                    'longitude' => $addressData['longitude'],
                    'kolejnosc_dostawy' => $index + 1,
                    'uwagi_dostawy' => $dateIndex === 0 ? $this->getUrgentDeliveryNote() : null,
                    'godzina_rozpoczecia' => $status === 'w_drodze' ?
                        $date->copy()->subHours(rand(1, 2)) : null,
                ]);

                // Create delivery items
                foreach ($productionOrder->items->take(rand(2, 4)) as $orderItem) {
                    $deliveryQuantity = rand(1, min($orderItem->ilosc_wyprodukowana, 10));
                    $deliveredQuantity = $status === 'w_drodze' ? rand(0, $deliveryQuantity) : 0;

                    DeliveryItem::create([
                        'delivery_id' => $delivery->id,
                        'product_id' => $orderItem->product_id,
                        'production_order_item_id' => $orderItem->id,
                        'nazwa_produktu' => $orderItem->product->nazwa,
                        'ilosc' => $deliveryQuantity,
                        'jednostka' => $orderItem->jednostka,
                        'ilosc_dostarczona' => $deliveredQuantity,
                        'waga_kg' => $orderItem->product->waga_g ?
                            ($orderItem->product->waga_g * $deliveryQuantity) / 1000 : null,
                        'status' => $this->getItemStatus($deliveredQuantity, $deliveryQuantity),
                    ]);
                }
            }
        }
    }

    private function getItemStatus($delivered, $total)
    {
        if ($delivered === 0) return 'oczekujacy';
        if ($delivered >= $total) return 'dostarczony';
        return 'brakuje';
    }

    private function getRandomDeliveryNote()
    {
        $notes = [
            'Dostawa przez tylne wejście',
            'Zadzwonić przed przyjazdem',
            'Odbiór tylko w godzinach 8:00-16:00',
            'Zapakować w dodatkowe opakowania',
            'Klient płaci przy odbiorze',
            null, // Some deliveries without notes
            null,
        ];

        return $notes[array_rand($notes)];
    }

    private function getUrgentDeliveryNote()
    {
        $notes = [
            'PILNE - dostawa na dzisiaj!',
            'Klient czeka - priorytet wysoki',
            'Zamówienie na event - nie opóźniać',
            'Ważny klient - szczególna ostrożność',
        ];

        return $notes[array_rand($notes)];
    }
}
