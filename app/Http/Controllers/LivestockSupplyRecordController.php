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
            'grn_number' => ['required', 'string', 'max:255'],
            'livestock_type' => ['required', 'string', 'max:255'],
            'number_of_animals' => ['required', 'integer', 'min:0'],
            'total_weight_kg' => ['required', 'numeric', 'min:0'],
            'receipt_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Accepted', 'Partially Accepted', 'Rejected'])],
        ]);

        return LivestockSupplyRecord::create($data);
    }

    public function destroy(LivestockSupplyRecord $livestockSupplyRecord)
    {
        $livestockSupplyRecord->delete();

        return response()->noContent();
    }
}
