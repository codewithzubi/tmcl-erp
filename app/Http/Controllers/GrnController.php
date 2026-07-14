<?php

namespace App\Http\Controllers;

use App\Models\Grn;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GrnController extends Controller
{
    public function index(Request $request)
    {
        return Grn::with('supplier', 'gateEntry')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['grn_number'] ??= 'GRN-'.str_pad((string) (Grn::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return Grn::create($data);
    }

    public function show(Grn $grn)
    {
        return $grn->load('supplier', 'gateEntry', 'purchaseOrder', 'livestockInspections', 'barnAllocations', 'lots');
    }

    public function update(Request $request, Grn $grn)
    {
        $data = $request->validate($this->rules($grn->id));
        $grn->update($data);

        return $grn;
    }

    public function destroy(Grn $grn)
    {
        $grn->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'grn_number' => ['nullable', 'string', 'max:255', Rule::unique('grns', 'grn_number')->ignore($ignoreId)],
            'supplier_purchase_order_id' => ['nullable', 'exists:supplier_purchase_orders,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'gate_entry_id' => ['required', 'exists:gate_entries,id'],
            'receipt_date' => ['required', 'date'],
            'number_of_animals_received' => ['required', 'integer', 'min:0'],
            'total_weight_received' => ['required', 'numeric', 'min:0'],
            'receiving_officer' => ['required', 'string', 'max:255'],
            'inspection_status' => ['required', Rule::in(['Pending', 'Passed', 'Failed', 'Partial'])],
            'accepted_animals' => ['nullable', 'integer', 'min:0'],
            'rejected_animals' => ['nullable', 'integer', 'min:0'],
            'accepted_weight' => ['nullable', 'numeric', 'min:0'],
            'rejected_weight' => ['nullable', 'numeric', 'min:0'],
            'rejection_reason' => ['nullable', 'string'],
            'veterinary_remarks' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['Draft', 'Completed', 'Cancelled'])],
        ];
    }
}
