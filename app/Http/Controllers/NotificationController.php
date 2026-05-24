<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Trang lịch sử thông báo
     */
    public function index()
    {
        $unreadCount = Auth::user()->unreadNotifications()->count();
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * API: Lấy danh sách thông báo mới nhất (dùng cho polling)
     */
    public function fetch()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->take(15)
            ->get()
            ->map(fn($n) => [
                'id'       => $n->id,
                'data'     => $n->data,
                'read_at'  => $n->read_at,
                'time'     => $n->created_at?->diffForHumans(),
                'time_fmt' => $n->created_at?->format('H:i, d/m/Y'),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a single notification as read
     */
    public function markRead(string $id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (!$request->expectsJson() && !$request->ajax()) {
            return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
        }

        return response()->json(['success' => true]);
    }
}
