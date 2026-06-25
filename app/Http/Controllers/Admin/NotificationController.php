<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Menampilkan halaman notifikasi
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unread, order, stock, review, return, membership, system
        $search = $request->get('search', '');
        
        // Query notifikasi
        $query = Notification::query();
        
        // Filter by type
        if ($filter !== 'all') {
            if ($filter === 'unread') {
                $query->where('is_read', false);
            } elseif ($filter === 'membership') {
                $query->where(function ($q) {
                    $q->where('type', 'like', 'membership_%')
                        ->orWhereIn('type', ['member_new_product', 'member_special_promo']);
                });
            } else {
                $query->where('type', $filter);
            }
        }
        
        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        
        // Get counts for metrics
        $unreadCount = Notification::where('is_read', false)->count();
        $todayCount = Notification::whereDate('created_at', today())->count();
        $totalCount = Notification::count();
        
        // Get notifications
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.pages.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'todayCount' => $todayCount,
            'totalCount' => $totalCount,
            'currentFilter' => $filter,
            'currentSearch' => $search,
            'activePage' => 'notifications'
        ]);
    }
    
    /**
     * Mark single notification as read/unread
     */
    public function toggleRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = !$notification->is_read;
        $notification->save();
        
        return response()->json([
            'success' => true,
            'is_read' => $notification->is_read
        ]);
    }
    
    /**
     * Mark all as read
     */
    public function markAllRead()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }
    
    /**
     * Delete single notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus.'
        ]);
    }
    
    /**
     * Clear all read notifications
     */
    public function clearRead()
    {
        Notification::where('is_read', true)->delete();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Semua notifikasi yang sudah dibaca telah dihapus.');
    }
    
    /**
     * Create notification (helper method)
     */
    public static function create($type, $title, $message, $link = null, $priority = 'normal')
    {
        return Notification::create([
            'user_id' => null, // For admin, set null or specific admin user_id
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'priority' => $priority,
            'is_read' => false,
        ]);
    }
}
