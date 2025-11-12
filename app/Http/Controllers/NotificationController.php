<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['ok' => false], 401);
        }

        // tandai semua unread sebagai read
        $user->unreadNotifications->markAsRead();

        return response()->json(['ok' => true, 'unread_count' => 0]);
    }
}
