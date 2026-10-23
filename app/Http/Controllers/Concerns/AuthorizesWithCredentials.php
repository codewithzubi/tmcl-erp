<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// 20 Oct 2026 points 3/14 — sensitive actions (Reopen Slaughter, Tag
// transfer, Merge Slaughter IDs) need a user to re-enter their own email and
// password on the spot, and that user's role must grant the action. Mirrors
// the frontend's usePermissions().canDo(): no role = allowed, fullAccess =
// allowed, otherwise modules[module][page].custom[actionKey].
trait AuthorizesWithCredentials
{
    protected function authorizeWithCredentials(Request $request, string $module, string $page, string $actionKey): User
    {
        $data = $request->validate([
            'authorizer_email' => ['required', 'email'],
            'authorizer_password' => ['required', 'string'],
        ]);

        $user = User::with('role')->where('email', $data['authorizer_email'])->first();

        // 422, not 401 — a 401 would sign the current session out.
        if (! $user || ! Hash::check($data['authorizer_password'], $user->password)) {
            throw ValidationException::withMessages([
                'authorizer_email' => ['The email or password is incorrect.'],
            ]);
        }

        if ($user->status === 'Inactive') {
            throw ValidationException::withMessages([
                'authorizer_email' => ['This user account is inactive.'],
            ]);
        }

        $permissions = $user->role?->permissions;
        $allowed = ! $permissions
            || ! empty($permissions['fullAccess'])
            || ! empty($permissions['modules'][$module][$page]['custom'][$actionKey]);

        if (! $allowed) {
            abort(403, "{$user->name} does not have permission for this action.");
        }

        return $user;
    }
}
