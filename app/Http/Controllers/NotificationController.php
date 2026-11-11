<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        $unreadCount = auth()->user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(DatabaseNotification $notification)
    {
        // Authorization: only owner can mark
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403, 'No autorizado.');
        }

        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notificación marcada como leída.');
    }
}
