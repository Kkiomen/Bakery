<?php

namespace App\Console\Commands;

use App\Models\B2BPricing;
use App\Models\Product;
use Illuminate\Console\Command;

class ShowB2BPricing extends Command
{
    protected $signature = 'b2b:show-pricing {product_id?}';
    protected $description = 'Display B2B pricing information';

    public function handle()
    {
        $productId = $this->argument('product_id');

        if ($productId) {
            $this->showProductPricing($productId);
        } else {
            $this->showOverview();
        }
    }

    private function showOverview()
    {
        $this->info('🏪 PRZEGLĄD CENNIKÓW B2B');
        $this->info('========================');

        $totalPricings = B2BPricing::count();
        $this->info("📊 Łącznie cenników: {$totalPricings}");

        $this->newLine();
        $this->info('📋 PRZYKŁADOWE CENNIKI (STANDARD):');

        $standardPricings = B2BPricing::with('product')
            ->where('pricing_tier', 'standard')
            ->orderBy('product_id')
            ->orderBy('min_quantity')
            ->take(15)
            ->get();

        $currentProduct = null;
        foreach ($standardPricings as $pricing) {
            if ($currentProduct !== $pricing->product->nazwa) {
                $this->newLine();
                $this->line("🍞 <comment>{$pricing->product->nazwa}</comment>");
                $currentProduct = $pricing->product->nazwa;
            }

            $range = $pricing->max_quantity ?
                "{$pricing->min_quantity}-{$pricing->max_quantity}" :
                "{$pricing->min_quantity}+";

            $discount = $pricing->discount_percent > 0 ?
                " <fg=red>(-{$pricing->discount_percent}%)</>" : "";

            $this->line("   {$range} szt: <info>{$pricing->price_net} zł</info>{$discount}");
        }

        $this->newLine();
        $this->info('💎 POZIOMY CENOWE:');
        $tiers = B2BPricing::select('pricing_tier')
            ->distinct()
            ->pluck('pricing_tier')
            ->sort();

        foreach ($tiers as $tier) {
            $count = B2BPricing::where('pricing_tier', $tier)->count();
            $this->line("   {$tier}: {$count} cenników");
        }

        $this->newLine();
        $this->info('🎯 SPECJALNE PROMOCJE:');
        $specialPricings = B2BPricing::whereNotNull('b2_b_client_id')
            ->orWhereNotNull('valid_from')
            ->count();
        $this->line("   Specjalnych cenników: {$specialPricings}");
    }

    private function showProductPricing($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            $this->error("Produkt o ID {$productId} nie istnieje.");
            return;
        }

        $this->info("🍞 CENNIK DLA: {$product->nazwa}");
        $this->info(str_repeat('=', 50));

        $pricings = B2BPricing::where('product_id', $productId)
            ->orderBy('pricing_tier')
            ->orderBy('min_quantity')
            ->get();

        if ($pricings->isEmpty()) {
            $this->warn('Brak cenników dla tego produktu.');
            return;
        }

        $currentTier = null;
        foreach ($pricings as $pricing) {
            if ($currentTier !== $pricing->pricing_tier) {
                $this->newLine();
                $this->line("💎 <comment>{$pricing->pricing_tier}</comment>");
                $currentTier = $pricing->pricing_tier;
            }

            $range = $pricing->max_quantity ?
                "{$pricing->min_quantity}-{$pricing->max_quantity}" :
                "{$pricing->min_quantity}+";

            $discount = $pricing->discount_percent > 0 ?
                " <fg=red>(-{$pricing->discount_percent}%)</>" : "";

            $special = $pricing->b2_b_client_id ? " <fg=yellow>[VIP]</>" : "";
            $seasonal = $pricing->valid_from ? " <fg=cyan>[PROMOCJA]</>" : "";

            $this->line("   {$range} szt: <info>{$pricing->price_net} zł</info>{$discount}{$special}{$seasonal}");
        }
    }
}
