<?php

namespace App\Http\Controllers;

use App\Models\LivestockSupplyRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LivestockSupplyRecordController extends Controller
{
    public function index(Request $request)
    {
        return LivestockSupplyRecord::query()
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'grn_number' => ['nullable', 'string', 'max:255'],
            'livestock_type' => ['nullable', 'string', 'max:255'],
            'number_of_animals' => ['nullable', 'integer', 'min:0'],
            'total_weight_kg' => ['nullable', 'numeric', 'min:0'],
            'receipt_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['Accepted', 'Partially Accepted', 'Rejected'])],
        ]);

        return LivestockSupplyRecord::create($data);
    }

    public function destroy(LivestockSupplyRecord $livestockSupplyRecord)
    {
        $livestockSupplyRecord->delete();

        return response()->noContent();
    }
}
