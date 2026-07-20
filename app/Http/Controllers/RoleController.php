<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        return Role::withCount('users')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        return Role::create($data);
    }

    public function show(Role $role)
    {
        return $role->load('users');
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'display_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $role->update($data);

        return $role;
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->noContent();
    }
}
