<?php

namespace App\Http\Controllers;

use App\Models\StorageUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StorageUnitController extends Controller
{
    public function index(Request $request)
    {
        return StorageUnit::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['occupied_kg'] ??= 0;

        return StorageUnit::create($data);
    }

    public function show(StorageUnit $storageUnit)
    {
        return $storageUnit->load('sessions');
    }

    public function update(Request $request, StorageUnit $storageUnit)
    {
        $data = $request->validate($this->rules($storageUnit->id));
        $storageUnit->update($data);

        return $storageUnit;
    }

    public function destroy(StorageUnit $storageUnit)
    {
        $storageUnit->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'unit_code' => ['required', 'string', 'max:255', Rule::unique('storage_units', 'unit_code')->ignore($ignoreId)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['Chiller', 'Blast Freezer', 'Freezer'])],
            'capacity_kg' => ['required', 'numeric', 'min:0'],
            'occupied_kg' => ['nullable', 'numeric', 'min:0'],
            'min_temp' => ['required', 'numeric'],
            'max_temp' => ['required', 'numeric'],
            'target_temp' => ['required', 'numeric'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ];
    }
}
