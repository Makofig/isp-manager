<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationsBell extends Component
{
    public $unreadCount = 0;
    public $notifications = [];
    public $showDropdown = false;

    protected $listeners = ['notificationReceived' => 'refreshNotifications'];

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        if (! auth()->check()) {
            return;
        }

        $this->notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();

        $this->unreadCount = auth()->user()->unreadNotifications()->count();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    public function markRead(string $id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            $this->refreshNotifications();
        }
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->refreshNotifications();
        session()->flash('success', 'Todas las notificaciones marcadas como leídas.');
    }

    public function render()
    {
        return view('livewire.notifications-bell');
    }
}
