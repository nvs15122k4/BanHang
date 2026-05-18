<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Show notification index page
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        return view('profile.notifications', compact('notifications'));
    }

    /**
     * Fetch unread count and recent notifications (AJAX Polling)
     */
    public function fetch()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'unread_count'  => 0,
                'notifications' => []
            ]);
        }

        $unreadCount = $user->unreadNotifications()->count();
        $recent      = $user->notifications()->take(5)->get()->map(function ($n) {
            return [
                'id'         => $n->id,
                'title'      => $n->data['title'] ?? 'Thông báo',
                'message'    => $n->data['message'] ?? '',
                'url'        => $n->data['url'] ?? '#',
                'created_at' => $n->created_at->diffForHumans(),
                'is_read'    => $n->read_at !== null,
            ];
        });

        return response()->json([
            'unread_count'  => $unreadCount,
            'notifications' => $recent
        ]);
    }

    /**
     * Mark single notification as read
     */
    public function markRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect($notification->data['url'] ?? route('home'));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã đánh dấu tất cả là đã đọc.']);
        }

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
