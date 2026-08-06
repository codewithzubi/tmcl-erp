<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    // A notification is visible to a user if it's addressed to them
    // specifically, or has no user_id at all (a broadcast to everyone).
    private function visibleToCurrentUser($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('user_id')->orWhere('user_id', auth()->id());
        });
    }

    public function index(Request $request)
    {
        return $this->visibleToCurrentUser(AppNotification::query())
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->boolean('unread_only'), fn ($q) => $q->where('read', false))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'category' => ['required', Rule::in(['Event', 'Alert'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        return AppNotification::create($data);
    }

    public function markRead(AppNotification $notification)
    {
        $notification->update(['read' => true]);

        return $notification;
    }

    public function markAllRead(Request $request)
    {
        $this->visibleToCurrentUser(AppNotification::query())->update(['read' => true]);

        return response()->noContent();
    }

    public function destroy(AppNotification $notification)
    {
        $notification->delete();

        return response()->noContent();
    }
}
