<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return AppNotification::query()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->boolean('unread_only'), fn ($q) => $q->where('read', false))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'category' => ['required', Rule::in(['Event', 'Stock', 'Alert'])],
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
        AppNotification::query()
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->update(['read' => true]);

        return response()->noContent();
    }

    public function destroy(AppNotification $notification)
    {
        $notification->delete();

        return response()->noContent();
    }
}
