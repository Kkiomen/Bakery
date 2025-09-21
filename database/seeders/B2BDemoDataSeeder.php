<?php

namespace Database\Seeders;

use App\Models\B2BClient;
use App\Models\B2BOrder;
use App\Models\B2BOrderItem;
use App\Models\RecurringOrder;
use App\Models\Product;
use App\Notifications\B2BOrderStatusChanged;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class B2BDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏗️ Tworzenie danych demonstracyjnych B2B...');

        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->warn('⚠️ Brak produktów - uruchom najpierw ProductSeeder');
            return;
        }

        $clients = B2BClient::all();
        if ($clients->isEmpty()) {
            $this->command->warn('⚠️ Brak klientów B2B - uruchom najpierw B2BClientSeeder');
            return;
        }

        $this->createDemoOrders($clients, $products);
        $this->createRecurringOrders($clients, $products);
        $this->createNotifications($clients);

        $this->command->info('✅ Dane demonstracyjne B2B zostały utworzone!');
    }

    private function createDemoOrders($clients, $products)
    {
        $this->command->info('📋 Tworzenie przykładowych zamówień...');

        foreach ($clients as $client) {
            $orderCount = rand(5, 8);

            for ($i = 0; $i < $orderCount; $i++) {
                $orderDate = Carbon::now()->subDays(rand(1, 90));
                $deliveryDate = $orderDate->copy()->addDays(rand(1, 7));

                $order = B2BOrder::create([
                    'b2_b_client_id' => $client->id,
                    'order_number' => $this->generateOrderNumber($orderDate),
                    'order_date' => $orderDate->toDateString(),
                    'delivery_date' => $deliveryDate->toDateString(),
                    'status' => ['pending', 'confirmed', 'delivered'][array_rand(['pending', 'confirmed', 'delivered'])],
                    'order_type' => 'one_time',
                    'delivery_address' => $client->address,
                    'delivery_postal_code' => $client->postal_code,
                    'delivery_city' => $client->city,
                    'payment_method' => 'credit',
                    'payment_due_date' => $orderDate->copy()->addDays(14),
                    'payment_status' => 'pending',
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate->copy()->addHours(rand(1, 48)),
                ]);

                $this->addOrderItems($order, $products, $client);
            }
        }

        $this->command->info("✅ Utworzono " . B2BOrder::count() . " zamówień");
    }

    private function addOrderItems($order, $products, $client)
    {
        $itemCount = rand(2, 6);
        $selectedProducts = $products->random($itemCount);

        $subtotal = 0;
        $taxAmount = 0;

        foreach ($selectedProducts as $product) {
            $quantity = rand(1, 20);
            $pricing = $client->getPriceForProduct($product, $quantity);

            if ($pricing) {
                $unitPrice = $pricing->price_net;
                $unitPriceGross = $pricing->price_gross;
            } else {
                $unitPrice = rand(200, 800) / 100;
                $unitPriceGross = $unitPrice * 1.23;
            }

            $lineTotal = $quantity * $unitPrice;
            $lineTotalGross = $quantity * $unitPriceGross;

            B2BOrderItem::create([
                'b2_b_order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->nazwa,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_price_gross' => $unitPriceGross,
                'line_total' => $lineTotal,
                'line_total_gross' => $lineTotalGross,
                'tax_rate' => 23,
                'tax_amount' => $lineTotalGross - $lineTotal,
            ]);

            $subtotal += $lineTotal;
            $taxAmount += ($lineTotalGross - $lineTotal);
        }

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $subtotal + $taxAmount,
        ]);
    }

    private function createRecurringOrders($clients, $products)
    {
        $this->command->info('🔄 Tworzenie zamówień cyklicznych...');

        foreach ($clients->take(3) as $client) {
            $selectedProducts = $products->random(3);
            $orderItems = [];
            $estimatedTotal = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(5, 15);
                $pricing = $client->getPriceForProduct($product, $quantity);

                $unitPrice = $pricing ? $pricing->price_net : rand(200, 600) / 100;
                $unitPriceGross = $pricing ? $pricing->price_gross : $unitPrice * 1.23;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->nazwa,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'unit_price_gross' => $unitPriceGross,
                    'tax_rate' => 23,
                    'discount_percent' => 0,
                ];

                $estimatedTotal += $quantity * $unitPriceGross;
            }

            $recurringOrder = RecurringOrder::create([
                'b2_b_client_id' => $client->id,
                'name' => 'Cotygodniowa dostawa - ' . $client->company_name,
                'description' => 'Regularna dostawa produktów',
                'frequency' => 'weekly',
                'schedule_config' => [
                    'interval' => 1,
                    'weekdays' => [1],
                    'delivery_days_ahead' => 1,
                ],
                'start_date' => now()->addDay()->toDateString(),
                'order_items' => $orderItems,
                'estimated_total' => $estimatedTotal,
                'delivery_address' => $client->address,
                'delivery_postal_code' => $client->postal_code,
                'delivery_city' => $client->city,
                'auto_confirm' => false,
                'days_before_notification' => 1,
                'is_active' => true,
            ]);

            $recurringOrder->update([
                'next_generation_at' => $recurringOrder->calculateNextGenerationDate(),
            ]);
        }

        $this->command->info("✅ Utworzono " . RecurringOrder::count() . " zamówień cyklicznych");
    }

    private function createNotifications($clients)
    {
        $this->command->info('🔔 Tworzenie przykładowych powiadomień...');

        foreach ($clients->take(2) as $client) {
            $recentOrders = $client->orders()->latest()->take(2)->get();

            foreach ($recentOrders as $order) {
                $client->notify(new B2BOrderStatusChanged($order, 'pending', $order->status));
            }
        }

        $notificationCount = \DB::table('notifications')->count();
        $this->command->info("✅ Utworzono {$notificationCount} powiadomień");
    }

    private function generateOrderNumber($date): string
    {
        $prefix = 'B2B';
        $dateStr = $date->format('Ymd');
        $sequence = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$dateStr}-{$sequence}";
    }
}
