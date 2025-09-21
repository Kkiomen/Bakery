<?php

namespace Database\Seeders;

use App\Models\B2BPricing;
use App\Models\Product;
use Illuminate\Database\Seeder;

class B2BPricingSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->info('Brak produktów - pomiń tworzenie cenników B2B');
            return;
        }

        $pricingTiers = [
            'standard' => ['multiplier' => 1.0, 'discount' => 0],
            'bronze' => ['multiplier' => 0.95, 'discount' => 5],
            'silver' => ['multiplier' => 0.90, 'discount' => 10],
            'gold' => ['multiplier' => 0.85, 'discount' => 15],
            'platinum' => ['multiplier' => 0.80, 'discount' => 20],
        ];

        $quantityTiers = [
            ['min' => 1, 'max' => 9, 'discount' => 0],
            ['min' => 10, 'max' => 49, 'discount' => 5],
            ['min' => 50, 'max' => 99, 'discount' => 10],
            ['min' => 100, 'max' => null, 'discount' => 15],
        ];

        foreach ($products as $product) {
            // Przykładowe ceny bazowe (można dostosować)
            $basePriceNet = match($product->kategoria_id) {
                1 => 3.50, // Chleby
                2 => 2.80, // Bułki
                3 => 4.20, // Ciasta
                default => 3.00,
            };

            foreach ($pricingTiers as $tier => $tierData) {
                foreach ($quantityTiers as $qtyTier) {
                    $adjustedPriceNet = $basePriceNet * $tierData['multiplier'];
                    $totalDiscount = $tierData['discount'] + $qtyTier['discount'];
                    $finalPriceNet = $adjustedPriceNet * (1 - $totalDiscount / 100);
                    $finalPriceGross = $finalPriceNet * 1.23; // VAT 23%

                    B2BPricing::create([
                        'product_id' => $product->id,
                        'pricing_tier' => $tier,
                        'price_net' => round($finalPriceNet, 2),
                        'price_gross' => round($finalPriceGross, 2),
                        'tax_rate' => 23.00,
                        'min_quantity' => $qtyTier['min'],
                        'max_quantity' => $qtyTier['max'],
                        'discount_percent' => $totalDiscount,
                        'is_active' => true,
                        'priority' => $qtyTier['min'], // Wyższe ilości = wyższy priorytet
                    ]);
                }
            }
        }

        // Dodaj kilka specjalnych promocji
        $this->createSpecialPromotions();
    }

    private function createSpecialPromotions()
    {
        $products = Product::limit(3)->get();

        foreach ($products as $product) {
            // Promocja weekendowa - 20% taniej
            B2BPricing::create([
                'product_id' => $product->id,
                'pricing_tier' => 'standard',
                'price_net' => 2.50,
                'price_gross' => 3.08,
                'tax_rate' => 23.00,
                'min_quantity' => 20,
                'max_quantity' => null,
                'discount_percent' => 20,
                'valid_from' => now()->startOfWeek()->addDays(5), // Piątek
                'valid_to' => now()->startOfWeek()->addDays(6), // Sobota
                'is_active' => true,
                'priority' => 100,
                'conditions' => [
                    'type' => 'weekend_special',
                    'description' => 'Specjalna cena weekendowa'
                ]
            ]);
        }

        // VIP ceny dla najlepszego klienta
        $vipClient = \App\Models\B2BClient::where('pricing_tier', 'platinum')->first();
        if ($vipClient) {
            foreach ($products as $product) {
                B2BPricing::create([
                    'product_id' => $product->id,
                    'pricing_tier' => 'platinum',
                    'b2_b_client_id' => $vipClient->id,
                    'price_net' => 2.00,
                    'price_gross' => 2.46,
                    'tax_rate' => 23.00,
                    'min_quantity' => 1,
                    'max_quantity' => null,
                    'discount_percent' => 25,
                    'is_active' => true,
                    'priority' => 200,
                    'conditions' => [
                        'type' => 'vip_pricing',
                        'description' => 'Specjalne ceny VIP'
                    ]
                ]);
            }
        }
    }
}
