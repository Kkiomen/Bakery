<?php

namespace Database\Seeders;

use App\Models\B2BPricing;
use App\Models\Product;
use App\Models\B2BClient;
use Illuminate\Database\Seeder;

class B2BQuantityDiscountSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->info('Brak produktów - pomiń tworzenie rabatów ilościowych');
            return;
        }

        $this->command->info('Tworzenie rabatów ilościowych B2B...');

        // Wyczyść istniejące cenniki B2B
        B2BPricing::truncate();

        $this->createStandardPricing($products);
        $this->createSpecialClientPricing();
        $this->createSeasonalPromotions($products);
        $this->createBulkOrderDiscounts($products);

        $this->command->info('Utworzono rabaty ilościowe dla ' . $products->count() . ' produktów');
    }

    private function createStandardPricing($products)
    {
        $pricingTiers = [
            'standard' => ['base_multiplier' => 1.0, 'tier_discount' => 0],
            'bronze' => ['base_multiplier' => 0.95, 'tier_discount' => 5],
            'silver' => ['base_multiplier' => 0.90, 'tier_discount' => 10],
            'gold' => ['base_multiplier' => 0.85, 'tier_discount' => 15],
            'platinum' => ['base_multiplier' => 0.80, 'tier_discount' => 20],
        ];

        // Różne progi rabatowe dla różnych typów produktów
        $quantityTiers = [
            'chleby' => [
                ['min' => 1, 'max' => 9, 'discount' => 0],
                ['min' => 10, 'max' => 24, 'discount' => 3],
                ['min' => 25, 'max' => 49, 'discount' => 6],
                ['min' => 50, 'max' => 99, 'discount' => 10],
                ['min' => 100, 'max' => null, 'discount' => 15],
            ],
            'bulki' => [
                ['min' => 1, 'max' => 19, 'discount' => 0],
                ['min' => 20, 'max' => 49, 'discount' => 4],
                ['min' => 50, 'max' => 99, 'discount' => 8],
                ['min' => 100, 'max' => 199, 'discount' => 12],
                ['min' => 200, 'max' => null, 'discount' => 18],
            ],
            'ciasta' => [
                ['min' => 1, 'max' => 4, 'discount' => 0],
                ['min' => 5, 'max' => 9, 'discount' => 5],
                ['min' => 10, 'max' => 19, 'discount' => 10],
                ['min' => 20, 'max' => null, 'discount' => 15],
            ],
            'default' => [
                ['min' => 1, 'max' => 9, 'discount' => 0],
                ['min' => 10, 'max' => 49, 'discount' => 5],
                ['min' => 50, 'max' => 99, 'discount' => 10],
                ['min' => 100, 'max' => null, 'discount' => 15],
            ]
        ];

        foreach ($products as $product) {
            // Określ ceny bazowe na podstawie kategorii
            $basePriceNet = $this->getBasePrice($product);

            // Wybierz odpowiednie progi ilościowe
            $productTiers = $this->getQuantityTiers($product, $quantityTiers);

            foreach ($pricingTiers as $tier => $tierData) {
                foreach ($productTiers as $index => $qtyTier) {
                    $tierPriceNet = $basePriceNet * $tierData['base_multiplier'];
                    $totalDiscount = $tierData['tier_discount'] + $qtyTier['discount'];
                    $finalPriceNet = $tierPriceNet * (1 - $totalDiscount / 100);
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
                        'priority' => 10 + $index, // Wyższe ilości = wyższy priorytet
                        'conditions' => [
                            'type' => 'quantity_discount',
                            'tier' => $tier,
                            'category' => $product->category->nazwa ?? 'inne'
                        ]
                    ]);
                }
            }
        }
    }

    private function createSpecialClientPricing()
    {
        $specialClients = B2BClient::whereIn('pricing_tier', ['gold', 'platinum'])->get();
        $products = Product::limit(5)->get(); // Tylko dla wybranych produktów

        foreach ($specialClients as $client) {
            foreach ($products as $product) {
                $basePrice = $this->getBasePrice($product);
                $vipDiscount = $client->pricing_tier === 'platinum' ? 25 : 20;
                $vipPrice = $basePrice * (1 - $vipDiscount / 100);

                B2BPricing::create([
                    'product_id' => $product->id,
                    'pricing_tier' => $client->pricing_tier,
                    'b2_b_client_id' => $client->id,
                    'price_net' => round($vipPrice, 2),
                    'price_gross' => round($vipPrice * 1.23, 2),
                    'tax_rate' => 23.00,
                    'min_quantity' => 1,
                    'max_quantity' => null,
                    'discount_percent' => $vipDiscount,
                    'is_active' => true,
                    'priority' => 100, // Najwyższy priorytet
                    'conditions' => [
                        'type' => 'vip_pricing',
                        'client_id' => $client->id,
                        'description' => 'Specjalne ceny VIP dla ' . $client->company_name
                    ]
                ]);
            }
        }
    }

    private function createSeasonalPromotions($products)
    {
        // Promocja świąteczna (grudzień)
        $holidayProducts = $products->where('kategoria_id', 3)->take(3); // Ciasta

        foreach ($holidayProducts as $product) {
            $basePrice = $this->getBasePrice($product);
            $holidayPrice = $basePrice * 0.85; // 15% taniej

            B2BPricing::create([
                'product_id' => $product->id,
                'pricing_tier' => 'standard',
                'price_net' => round($holidayPrice, 2),
                'price_gross' => round($holidayPrice * 1.23, 2),
                'tax_rate' => 23.00,
                'min_quantity' => 5,
                'max_quantity' => null,
                'discount_percent' => 15,
                'valid_from' => now()->month(12)->startOfMonth(),
                'valid_to' => now()->month(12)->endOfMonth(),
                'is_active' => true,
                'priority' => 80,
                'conditions' => [
                    'type' => 'seasonal_promotion',
                    'season' => 'christmas',
                    'description' => 'Promocja świąteczna na ciasta'
                ]
            ]);
        }

        // Promocja letnia (czerwiec-sierpień)
        $summerProducts = $products->where('kategoria_id', 6)->take(2); // Przekąski

        foreach ($summerProducts as $product) {
            $basePrice = $this->getBasePrice($product);
            $summerPrice = $basePrice * 0.90; // 10% taniej

            B2BPricing::create([
                'product_id' => $product->id,
                'pricing_tier' => 'standard',
                'price_net' => round($summerPrice, 2),
                'price_gross' => round($summerPrice * 1.23, 2),
                'tax_rate' => 23.00,
                'min_quantity' => 20,
                'max_quantity' => null,
                'discount_percent' => 10,
                'valid_from' => now()->month(6)->startOfMonth(),
                'valid_to' => now()->month(8)->endOfMonth(),
                'is_active' => true,
                'priority' => 70,
                'conditions' => [
                    'type' => 'seasonal_promotion',
                    'season' => 'summer',
                    'description' => 'Letnia promocja na przekąski'
                ]
            ]);
        }
    }

    private function createBulkOrderDiscounts($products)
    {
        // Mega rabaty dla bardzo dużych zamówień
        $bulkProducts = $products->whereIn('kategoria_id', [1, 2])->take(5); // Chleby i bułki

        foreach ($bulkProducts as $product) {
            $basePrice = $this->getBasePrice($product);

            // Rabat 20% dla zamówień 500+ sztuk
            B2BPricing::create([
                'product_id' => $product->id,
                'pricing_tier' => 'standard',
                'price_net' => round($basePrice * 0.80, 2),
                'price_gross' => round($basePrice * 0.80 * 1.23, 2),
                'tax_rate' => 23.00,
                'min_quantity' => 500,
                'max_quantity' => 999,
                'discount_percent' => 20,
                'is_active' => true,
                'priority' => 90,
                'conditions' => [
                    'type' => 'bulk_discount',
                    'description' => 'Rabat hurtowy 500-999 sztuk'
                ]
            ]);

            // Rabat 25% dla zamówień 1000+ sztuk
            B2BPricing::create([
                'product_id' => $product->id,
                'pricing_tier' => 'standard',
                'price_net' => round($basePrice * 0.75, 2),
                'price_gross' => round($basePrice * 0.75 * 1.23, 2),
                'tax_rate' => 23.00,
                'min_quantity' => 1000,
                'max_quantity' => null,
                'discount_percent' => 25,
                'is_active' => true,
                'priority' => 95,
                'conditions' => [
                    'type' => 'bulk_discount',
                    'description' => 'Mega rabat hurtowy 1000+ sztuk'
                ]
            ]);
        }
    }

    private function getBasePrice($product): float
    {
        // Ceny bazowe na podstawie kategorii
        return match($product->kategoria_id) {
            1 => rand(250, 450) / 100, // Chleby: 2.50-4.50 zł
            2 => rand(180, 320) / 100, // Bułki: 1.80-3.20 zł
            3 => rand(580, 1200) / 100, // Ciasta: 5.80-12.00 zł
            4 => rand(220, 380) / 100, // Pieczywo słodkie: 2.20-3.80 zł
            5 => rand(420, 680) / 100, // Pieczywo specjalne: 4.20-6.80 zł
            6 => rand(150, 280) / 100, // Przekąski: 1.50-2.80 zł
            default => rand(200, 400) / 100, // Inne: 2.00-4.00 zł
        };
    }

    private function getQuantityTiers($product, $quantityTiers): array
    {
        $categoryName = strtolower($product->category->nazwa ?? '');

        if (str_contains($categoryName, 'chleb')) {
            return $quantityTiers['chleby'];
        } elseif (str_contains($categoryName, 'bułk')) {
            return $quantityTiers['bulki'];
        } elseif (str_contains($categoryName, 'ciast')) {
            return $quantityTiers['ciasta'];
        }

        return $quantityTiers['default'];
    }
}
