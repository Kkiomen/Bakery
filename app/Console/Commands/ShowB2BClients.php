<?php

namespace App\Console\Commands;

use App\Models\B2BClient;
use Illuminate\Console\Command;

class ShowB2BClients extends Command
{
    protected $signature = 'b2b:show-clients';
    protected $description = 'Display B2B client login information';

    public function handle()
    {
        $this->info('🏢 KLIENCI B2B - DANE LOGOWANIA');
        $this->info('================================');

        $clients = B2BClient::select('id', 'company_name', 'email', 'pricing_tier', 'status')
            ->orderBy('pricing_tier')
            ->orderBy('company_name')
            ->get();

        if ($clients->isEmpty()) {
            $this->warn('Brak klientów B2B w bazie danych.');
            return;
        }

        $this->table(
            ['ID', 'Firma', 'Email', 'Poziom', 'Status'],
            $clients->map(function ($client) {
                return [
                    $client->id,
                    $client->company_name,
                    $client->email,
                    $client->pricing_tier,
                    $client->status === 'active' ? '✅ Aktywny' : '❌ Nieaktywny'
                ];
            })
        );

        $this->newLine();
        $this->info('🔑 HASŁO DLA WSZYSTKICH KLIENTÓW: password123');
        $this->newLine();
        $this->info('🌐 URL LOGOWANIA: http://localhost:8000/b2b/login');

        $this->newLine();
        $this->info('📊 STATYSTYKI:');
        $activeCount = $clients->where('status', 'active')->count();
        $this->line("   Aktywnych klientów: {$activeCount}");

        $tierCounts = $clients->groupBy('pricing_tier')->map->count();
        foreach ($tierCounts as $tier => $count) {
            $this->line("   {$tier}: {$count} klientów");
        }
    }
}
