<?php

namespace App\Livewire\B2B;

use App\Models\B2BOrder;
use App\Models\RecurringOrder;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function mount()
    {
        if (!Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.login');
        }
    }

    public function getStats()
    {
        $client = Auth::guard('b2b')->user();

        return [
            'total_orders' => $client->orders()->count(),
            'pending_orders' => $client->orders()->whereIn('status', ['pending', 'confirmed'])->count(),
            'this_month_orders' => $client->orders()->whereMonth('created_at', now()->month)->count(),
            'current_balance' => $client->current_balance,
            'available_credit' => $client->available_credit,
            'credit_limit' => $client->credit_limit,
        ];
    }

    public function getRecentOrders()
    {
        $client = Auth::guard('b2b')->user();

        return $client->orders()
                     ->with(['items.product'])
                     ->orderBy('created_at', 'desc')
                     ->limit(5)
                     ->get();
    }

    public function getUpcomingDeliveries()
    {
        $client = Auth::guard('b2b')->user();

        return $client->orders()
                     ->whereIn('status', ['confirmed', 'in_production', 'ready', 'shipped'])
                     ->whereDate('delivery_date', '>=', today())
                     ->orderBy('delivery_date')
                     ->limit(5)
                     ->get();
    }

    public function getRecurringOrders()
    {
        $client = Auth::guard('b2b')->user();

        return $client->recurringOrders()
                     ->orderBy('is_active', 'desc')
                     ->orderBy('next_generation_at')
                     ->limit(5)
                     ->get();
    }

    public function render()
    {
        return view('livewire.b2-b.dashboard', [
            'stats' => $this->getStats(),
            'recentOrders' => $this->getRecentOrders(),
            'upcomingDeliveries' => $this->getUpcomingDeliveries(),
            'recurringOrders' => $this->getRecurringOrders(),
        ]);
    }
}
