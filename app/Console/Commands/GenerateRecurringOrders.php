<?php

namespace App\Console\Commands;

use App\Models\RecurringOrder;
use App\Notifications\B2BRecurringOrderGenerated;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateRecurringOrders extends Command
{
    protected $signature = 'orders:generate-recurring
                           {--dry-run : Show what would be generated without actually creating orders}
                           {--force : Force generation even if not due yet}';

    protected $description = 'Generate orders from active recurring order templates';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔄 Sprawdzanie zamówień cyklicznych...');

        $query = RecurringOrder::with(['client'])
            ->active();

        if (!$force) {
            $query->dueForGeneration();
        }

        $recurringOrders = $query->get();

        if ($recurringOrders->isEmpty()) {
            $this->info('✅ Brak zamówień cyklicznych do wygenerowania.');
            return 0;
        }

        $this->info("📋 Znaleziono {$recurringOrders->count()} zamówień cyklicznych do wygenerowania:");

        $generatedCount = 0;
        $errorCount = 0;

        foreach ($recurringOrders as $recurringOrder) {
            try {
                $this->line("  - {$recurringOrder->name} ({$recurringOrder->client->company_name})");

                if ($isDryRun) {
                    $this->line("    [DRY RUN] Zostałoby wygenerowane zamówienie na {$recurringOrder->calculateDeliveryDate()}");
                    continue;
                }

                // Sprawdź czy można wygenerować
                if (!$force && !$recurringOrder->shouldGenerate()) {
                    $this->warn("    ⚠️ Zamówienie nie jest gotowe do wygenerowania");
                    continue;
                }

                // Wygeneruj zamówienie
                try {
                    $order = $recurringOrder->generateOrder();
                } catch (\Exception $e) {
                    $this->error("    ❌ Błąd generowania: {$e->getMessage()}");
                    Log::error('Szczegółowy błąd generowania zamówienia cyklicznego', [
                        'recurring_order_id' => $recurringOrder->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    $errorCount++;
                    continue;
                }

                if ($order) {
                    $generatedCount++;
                    $this->info("    ✅ Wygenerowano zamówienie: {$order->order_number}");

                    // Wyślij powiadomienie
                    try {
                        $recurringOrder->client->notify(
                            new B2BRecurringOrderGenerated($order, $recurringOrder)
                        );
                        $this->line("    📧 Wysłano powiadomienie");
                    } catch (\Exception $e) {
                        $this->warn("    ⚠️ Błąd wysyłania powiadomienia: {$e->getMessage()}");
                        Log::warning('Błąd wysyłania powiadomienia o zamówieniu cyklicznym', [
                            'recurring_order_id' => $recurringOrder->id,
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                } else {
                    $errorCount++;
                    $this->error("    ❌ Nie udało się wygenerować zamówienia");
                    Log::error('Błąd generowania zamówienia cyklicznego', [
                        'recurring_order_id' => $recurringOrder->id,
                        'client_id' => $recurringOrder->b2_b_client_id,
                    ]);
                }

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("    ❌ Błąd: {$e->getMessage()}");
                Log::error('Błąd przetwarzania zamówienia cyklicznego', [
                    'recurring_order_id' => $recurringOrder->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        if ($isDryRun) {
            $this->info("\n🔍 DRY RUN - żadne zamówienia nie zostały utworzone");
            return 0;
        }

        // Podsumowanie
        $this->line("\n📊 Podsumowanie:");
        $this->info("✅ Wygenerowano: {$generatedCount} zamówień");

        if ($errorCount > 0) {
            $this->error("❌ Błędy: {$errorCount}");
        }

        // Sprawdź nadchodzące zamówienia cykliczne
        $this->showUpcomingOrders();

        return $errorCount > 0 ? 1 : 0;
    }

    private function showUpcomingOrders()
    {
        $upcomingOrders = RecurringOrder::active()
            ->where('next_generation_at', '>', now())
            ->where('next_generation_at', '<=', now()->addDays(7))
            ->orderBy('next_generation_at')
            ->with('client')
            ->get();

        if ($upcomingOrders->isNotEmpty()) {
            $this->line("\n📅 Nadchodzące zamówienia cykliczne (następne 7 dni):");

            foreach ($upcomingOrders as $order) {
                $daysUntil = now()->diffInDays($order->next_generation_at, false);
                $this->line("  - {$order->name} ({$order->client->company_name}) - za {$daysUntil} dni ({$order->next_generation_at->format('d.m.Y H:i')})");
            }
        }
    }
}
