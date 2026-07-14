<?php

namespace App\Http\Controllers;

use App\Models\StorageSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StorageSessionController extends Controller
{
    public function index(Request $request)
    {
        return StorageSession::with('storageUnit', 'lot')
            ->when($request->filled('storage_unit_id'), fn ($q) => $q->where('storage_unit_id', $request->storage_unit_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['session_number'] ??= 'SESS-'.str_pad((string) (StorageSession::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return DB::transaction(function () use ($data) {
            $session = StorageSession::create($data);

            if ($session->status === 'Active') {
                $session->storageUnit()->increment('occupied_kg', $session->product_weight);
            }

            return $session;
        });
    }

    public function show(StorageSession $storageSession)
    {
        return $storageSession->load('storageUnit', 'lot');
    }

    public function update(Request $request, StorageSession $storageSession)
    {
        $data = $request->validate($this->rules($storageSession->id));

        return DB::transaction(function () use ($data, $storageSession) {
            $wasActive = $storageSession->status === 'Active';
            $storageSession->update($data);

            if ($wasActive && $storageSession->status === 'Completed') {
                $storageSession->storageUnit()->decrement('occupied_kg', $storageSession->product_weight);
            }

            return $storageSession;
        });
    }

    public function destroy(StorageSession $storageSession)
    {
        if ($storageSession->status === 'Active') {
            $storageSession->storageUnit()->decrement('occupied_kg', $storageSession->product_weight);
        }

        $storageSession->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'session_number' => ['nullable', 'string', 'max:255', Rule::unique('storage_sessions', 'session_number')->ignore($ignoreId)],
            'storage_unit_id' => ['required', 'exists:storage_units,id'],
            'lot_id' => ['required', 'exists:lots,id'],
            'product_weight' => ['required', 'numeric', 'min:0'],
            'time_in' => ['required', 'date'],
            'time_out' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['Active', 'Completed'])],
        ];
    }
}
