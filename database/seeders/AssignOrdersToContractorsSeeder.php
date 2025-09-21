<?php

namespace Database\Seeders;

use App\Models\Contractor;
use App\Models\ProductionOrder;
use Illuminate\Database\Seeder;

class AssignOrdersToContractorsSeeder extends Seeder
{
    public function run(): void
    {
        // Pobierz kilku kontrahentów typu klient
        $clients = Contractor::clients()->limit(4)->get();

        if ($clients->isEmpty()) {
            $this->command->info('Brak kontrahentów typu klient - pomiń przypisywanie zleceń');
            return;
        }

        // Pobierz zlecenia produkcyjne
        $orders = ProductionOrder::limit(10)->get();

        if ($orders->isEmpty()) {
            $this->command->info('Brak zleceń produkcyjnych - pomiń przypisywanie');
            return;
        }

        // Przypisz losowo zlecenia do kontrahentów (około 60% zleceń)
        foreach ($orders as $index => $order) {
            if ($index % 3 !== 0) { // Przypisz 2/3 zleceń, 1/3 zostaw bez kontrahenta
                $randomClient = $clients->random();
                $order->update([
                    'contractor_id' => $randomClient->id,
                    'klient' => $randomClient->nazwa, // Zaktualizuj też pole klient
                ]);

                $this->command->info("Przypisano zlecenie {$order->numer_zlecenia} do {$randomClient->nazwa}");
            }
        }
    }
}
