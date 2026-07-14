<?php

namespace App\Http\Controllers;

use App\Models\SupplierSettlement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierSettlementController extends Controller
{
    public function index(Request $request)
    {
        return SupplierSettlement::with('supplier', 'lot')
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->filled('lot_id'), fn ($q) => $q->where('lot_id', $request->lot_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['settlement_number'] ??= 'SETL-'.str_pad((string) (SupplierSettlement::max('id') + 1), 4, '0', STR_PAD_LEFT);
        $data['total_settlement_amount'] = $data['agreed_rate_per_kg'] * $data['approved_meat_weight'];

        return SupplierSettlement::create($data);
    }

    public function show(SupplierSettlement $supplierSettlement)
    {
        return $supplierSettlement->load('supplier', 'lot');
    }

    public function update(Request $request, SupplierSettlement $supplierSettlement)
    {
        $data = $request->validate($this->rules($supplierSettlement->id));
        $data['total_settlement_amount'] = $data['agreed_rate_per_kg'] * $data['approved_meat_weight'];
        $supplierSettlement->update($data);

        return $supplierSettlement;
    }

    public function destroy(SupplierSettlement $supplierSettlement)
    {
        $supplierSettlement->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'settlement_number' => ['nullable', 'string', 'max:255', Rule::unique('supplier_settlements', 'settlement_number')->ignore($ignoreId)],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'lot_id' => ['required', 'exists:lots,id'],
            'agreed_rate_per_kg' => ['required', 'numeric', 'min:0'],
            'approved_meat_weight' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', Rule::in(['Bank Transfer', 'Cash', 'Cheque'])],
            'payment_date' => ['nullable', 'date'],
            'settlement_status' => ['required', Rule::in(['Pending', 'Approved', 'Paid'])],
            'approved_by' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
