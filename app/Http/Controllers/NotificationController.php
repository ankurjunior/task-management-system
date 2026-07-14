<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark the clicked notification as read and open its destination.
     */
    public function read(Request $request, Notification $notification): RedirectResponse
    {
        abort_unless((int) $notification->user_id === (int) $request->user()->id, 403);

        $notification->markAsRead();

        if ($notification->task_id) {
            return redirect()->route('tasks.show', $notification->task_id);
        }

        return redirect()->back();
    }
}