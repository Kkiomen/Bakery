<?php

namespace App\Livewire\B2B;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read
    public $selectedNotifications = [];
    public $selectAll = false;

    protected $queryString = [
        'filter' => ['except' => 'all'],
    ];

    public function mount()
    {
        if (!Auth::guard('b2b')->check()) {
            return redirect()->route('b2b.login');
        }
    }

    public function updatedFilter()
    {
        $this->resetPage();
        $this->selectedNotifications = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedNotifications = $this->getNotifications()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedNotifications = [];
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::guard('b2b')->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
            session()->flash('success', 'Powiadomienie zostało oznaczone jako przeczytane.');
        }
    }

    public function markAsUnread($notificationId)
    {
        $notification = Auth::guard('b2b')->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification && $notification->read_at) {
            $notification->update(['read_at' => null]);
            session()->flash('success', 'Powiadomienie zostało oznaczone jako nieprzeczytane.');
        }
    }

    public function markSelectedAsRead()
    {
        if (empty($this->selectedNotifications)) {
            session()->flash('error', 'Nie wybrano żadnych powiadomień.');
            return;
        }

        Auth::guard('b2b')->user()
            ->notifications()
            ->whereIn('id', $this->selectedNotifications)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->selectedNotifications = [];
        $this->selectAll = false;

        session()->flash('success', 'Wybrane powiadomienia zostały oznaczone jako przeczytane.');
    }

    public function deleteSelected()
    {
        if (empty($this->selectedNotifications)) {
            session()->flash('error', 'Nie wybrano żadnych powiadomień.');
            return;
        }

        Auth::guard('b2b')->user()
            ->notifications()
            ->whereIn('id', $this->selectedNotifications)
            ->delete();

        $this->selectedNotifications = [];
        $this->selectAll = false;

        session()->flash('success', 'Wybrane powiadomienia zostały usunięte.');
    }

    public function deleteNotification($notificationId)
    {
        Auth::guard('b2b')->user()
            ->notifications()
            ->where('id', $notificationId)
            ->delete();

        session()->flash('success', 'Powiadomienie zostało usunięte.');
    }

    public function markAllAsRead()
    {
        Auth::guard('b2b')->user()
            ->unreadNotifications
            ->markAsRead();

        session()->flash('success', 'Wszystkie powiadomienia zostały oznaczone jako przeczytane.');
    }

    public function deleteAll()
    {
        $query = Auth::guard('b2b')->user()->notifications();

        if ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }

        $count = $query->count();
        $query->delete();

        session()->flash('success', "Usunięto {$count} powiadomień.");
    }

    public function getNotifications()
    {
        $query = Auth::guard('b2b')->user()->notifications();

        // Filtrowanie
        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    public function getStats()
    {
        $user = Auth::guard('b2b')->user();

        return [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->notifications()->whereNotNull('read_at')->count(),
            'today' => $user->notifications()->whereDate('created_at', today())->count(),
        ];
    }

    public function render()
    {
        return view('livewire.b2-b.notifications', [
            'notifications' => $this->getNotifications(),
            'stats' => $this->getStats(),
        ]);
    }
}
