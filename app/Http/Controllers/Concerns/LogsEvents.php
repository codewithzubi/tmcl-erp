<?php

namespace App\Http\Controllers\Concerns;

use App\Models\EventLog;
use Illuminate\Support\Facades\Request as RequestFacade;

// Shared by controllers that back a "Field History" viewer on their screen:
// records an audit trail row every time a tracked record is created,
// updated, or deleted.
trait LogsEvents
{
    protected function logEvent(string $module, string $screen, string|int|null $recordId, string $action, ?string $newValue = null): void
    {
        EventLog::create([
            'user_id' => auth()->id(),
            'role' => auth()->user()?->role?->name,
            'module' => $module,
            'screen' => $screen,
            'record_id' => $recordId !== null ? (string) $recordId : null,
            'action' => $action,
            'new_value' => $newValue,
            'ip_address' => RequestFacade::ip(),
            'device_info' => RequestFacade::userAgent(),
            'logged_at' => now(),
        ]);
    }
}
