<?php

namespace App\Http\Controllers;

use App\Models\EventLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventLogController extends Controller
{
    public function index(Request $request)
    {
        return EventLog::with('user')
            ->when($request->filled('module'), fn ($q) => $q->where('module', $request->module))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->action))
            ->when($request->filled('record_id'), fn ($q) => $q->where('record_id', $request->record_id))
            ->latest('logged_at')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'role' => ['nullable', 'string'],
            'module' => ['required', 'string'],
            'screen' => ['required', 'string'],
            'record_id' => ['nullable', 'string'],
            'action' => ['required', Rule::in(['Create', 'Update', 'Delete', 'Approve', 'Reject', 'Login', 'Logout'])],
            'new_value' => ['nullable', 'string'],
            'ip_address' => ['nullable', 'string'],
            'device_info' => ['nullable', 'string'],
        ]);

        $data['ip_address'] ??= $request->ip();
        $data['device_info'] ??= $request->userAgent();
        $data['logged_at'] = now();

        return EventLog::create($data);
    }

    public function show(EventLog $eventLog)
    {
        return $eventLog->load('user');
    }

    public function destroy(EventLog $eventLog)
    {
        $eventLog->delete();

        return response()->noContent();
    }
}
