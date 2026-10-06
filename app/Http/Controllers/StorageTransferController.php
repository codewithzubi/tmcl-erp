<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsEvents;
use App\Models\StorageTransfer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Point 20 — internal Chiller/Blast Freezer transfers (moving a supplier's
// weight/tags/pieces from one storage location to another, not out of
// storage entirely — that's Meat Transfer/Export's job). Index-only plus
// store — a transfer is a point-in-time audit record, never edited.
class StorageTransferController extends Controller
{
    use LogsEvents;

    public function index(Request $request)
    {
        return StorageTransfer::with('supplier')
            ->when($request->filled('slaughter_record_id'), fn ($q) => $q->where('slaughter_record_id', $request->slaughter_record_id))
            ->latest('transferred_at')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'from_type' => ['required', Rule::in(['Chiller', 'Blast Freezer'])],
            'from_name' => ['required', 'string', 'max:255'],
            'to_type' => ['required', Rule::in(['Chiller', 'Blast Freezer'])],
            'to_name' => ['required', 'string', 'max:255'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'tag_ids' => ['nullable', 'array'],
            'weight' => ['required', 'numeric', 'min:0'],
            'comments' => ['nullable', 'string'],
            'close_time' => ['nullable', 'date'],
            'transferred_by' => ['nullable', 'string', 'max:255'],
        ]);

        $data['transferred_at'] = now();
        $transfer = StorageTransfer::create($data);

        $this->logEvent(
            'Cold Storage',
            'Cold Storage',
            $transfer->id,
            'Transfer',
            "{$data['weight']} kg from {$data['from_name']} to {$data['to_name']}" .
                (($data['comments'] ?? null) ? " — {$data['comments']}" : ''),
        );

        return $transfer->load('supplier');
    }
}
