<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Contractor;
use Carbon\Carbon;

class ProductionOrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $contractors = Contractor::active()->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Brak użytkowników lub produktów. Uruchom najpierw inne seedery.');
            return;
        }

        $statuses = ['oczekujace', 'w_produkcji', 'zakonczone'];
        $priorities = ['niski', 'normalny', 'wysoki', 'pilny'];
        $types = ['wewnetrzne', 'sklep', 'b2b', 'hotel'];

        // Zlecenia na dziś
        $this->createOrdersForDate(now(), $users, $products, $contractors, $statuses, $priorities, $types, 3);

        // Zlecenia na jutro
        $this->createOrdersForDate(now()->addDay(), $users, $products, $contractors, $statuses, $priorities, $types, 5);

        // Zlecenia na pojutrze
        $this->createOrdersForDate(now()->addDays(2), $users, $products, $contractors, $statuses, $priorities, $types, 4);

        // Zlecenia na następny tydzień
        for ($i = 3; $i <= 9; $i++) {
            $this->createOrdersForDate(
                now()->addDays($i),
                $users,
                $products,
                $contractors,
                $statuses,
                $priorities,
                $types,
                rand(1, 3)
            );
        }

        // Kilka opóźnionych zleceń (z przeszłości)
        $this->createOrdersForDate(now()->subDays(2), $users, $products, $contractors, ['oczekujace', 'w_produkcji'], $priorities, $types, 2);
        $this->createOrdersForDate(now()->subDay(), $users, $products, $contractors, ['oczekujace', 'w_produkcji'], $priorities, $types, 1);

        $this->command->info('Utworzono przykładowe zlecenia produkcji.');
    }

    private function createOrdersForDate(Carbon $date, $users, $products, $contractors, $statuses, $priorities, $types, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $status = $statuses[array_rand($statuses)];
            $priority = $priorities[array_rand($priorities)];
            $type = $types[array_rand($types)];
            $user = $users->random();

            $orderNames = [
                'Zamówienie sklep "Piekarnia Pod Kasztanem"',
                'Dostawa dla hotelu "Grand"',
                'Zamówienie B2B - Restauracja "Smaki"',
                'Produkcja na magazyn',
                'Specjalne zamówienie weselne',
                'Dostawa dla sklepu "Delikatesy"',
                'Zamówienie cateringowe',
                'Produkcja standardowa',
            ];

            // Losowo przypisz kontrahenta (70% szans)
            $contractor = null;
            $clientName = null;

            if (!$contractors->isEmpty() && rand(1, 10) <= 7) {
                $contractor = $contractors->random();
                $clientName = $contractor->nazwa;
            } else {
                // Fallback na losowe nazwy klientów
                $fallbackClients = [
                    'Sklep "Piekarnia Pod Kasztanem"',
                    'Hotel "Grand"',
                    'Restauracja "Smaki"',
                    'Państwo Kowalski',
                    'Delikatesy "Smaczne"',
                    'Firma cateringowa "Smakosze"',
                ];
                $clientName = $fallbackClients[array_rand($fallbackClients)];
            }

            $order = ProductionOrder::create([
                'nazwa' => $orderNames[array_rand($orderNames)],
                'opis' => rand(0, 1) ? 'Standardowe zamówienie zgodnie z ustaleniami.' : null,
                'data_produkcji' => $date,
                'user_id' => $user->id,
                'status' => $status,
                'priorytet' => $priority,
                'typ_zlecenia' => $type,
                'klient' => $clientName,
                'contractor_id' => $contractor?->id,
                'uwagi' => rand(0, 1) ? 'Proszę o szczególną uwagę na jakość.' : null,
                'data_rozpoczecia' => in_array($status, ['w_produkcji', 'zakonczone']) ? $date->copy()->subHours(rand(1, 8)) : null,
                'data_zakonczenia' => $status === 'zakonczone' ? $date->copy()->subHours(rand(0, 2)) : null,
            ]);

            // Dodaj pozycje do zlecenia
            $itemCount = rand(2, 6);
            $selectedProducts = $products->random($itemCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(10, 100);
                $producedQuantity = match($status) {
                    'oczekujace' => 0,
                    'w_produkcji' => rand(0, $quantity - 1),
                    'zakonczone' => $quantity,
                    default => 0,
                };

                $itemStatus = match($status) {
                    'oczekujace' => 'oczekujace',
                    'w_produkcji' => $producedQuantity >= $quantity ? 'zakonczone' : ($producedQuantity > 0 ? 'w_produkcji' : 'oczekujace'),
                    'zakonczone' => 'zakonczone',
                    default => 'oczekujace',
                };

                ProductionOrderItem::create([
                    'production_order_id' => $order->id,
                    'product_id' => $product->id,
                    'ilosc' => $quantity,
                    'jednostka' => 'szt',
                    'ilosc_wyprodukowana' => $producedQuantity,
                    'status' => $itemStatus,
                    'uwagi' => rand(0, 1) ? 'Standardowa jakość' : null,
                ]);
            }
        }
    }
}
