<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tampilkan halaman notifikasi (order + retur)
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil semua notifikasi user (order dan retur)
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('customer.pages.notifications', compact('notifications'));
    }

    /**
     * Mark notifikasi sebagai read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return redirect($notification->link ?: route('customer.notifications.index'));
    }

    /**
     * Mark all notifikasi sebagai read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai sudah dibaca');
    }
}
