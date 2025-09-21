<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionOrderItem;

class UpdateProductionOrderItemsSeeder extends Seeder
{
    public function run(): void
    {
        $items = ProductionOrderItem::all();

        foreach ($items as $item) {
            // Ustaw krok produkcji na podstawie statusu
            $currentStep = match($item->status) {
                'oczekujace' => 'waiting',
                'w_produkcji' => $this->getRandomProductionStep(),
                'zakonczone' => 'completed',
                default => 'waiting',
            };

            $item->update([
                'current_step' => $currentStep,
                'step_started_at' => $currentStep !== 'waiting' ? now()->subMinutes(rand(10, 180)) : null,
            ]);
        }

        $this->command->info('Zaktualizowano kroki produkcji dla ' . $items->count() . ' pozycji.');
    }

    private function getRandomProductionStep(): string
    {
        $steps = [
            'preparing',
            'mixing',
            'first_rise',
            'shaping',
            'second_rise',
            'baking',
            'cooling',
            'packaging'
        ];

        return $steps[array_rand($steps)];
    }
}
