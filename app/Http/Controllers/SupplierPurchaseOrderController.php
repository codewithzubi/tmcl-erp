<?php

namespace App\Http\Controllers;

use App\Models\SupplierPurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierPurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        return SupplierPurchaseOrder::with('supplier', 'purchaseRequisition')
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return SupplierPurchaseOrder::create($data);
    }

    public function show(SupplierPurchaseOrder $supplierPurchaseOrder)
    {
        return $supplierPurchaseOrder->load('supplier', 'purchaseRequisition', 'quotation', 'grns', 'lots');
    }

    public function update(Request $request, SupplierPurchaseOrder $supplierPurchaseOrder)
    {
        $data = $request->validate($this->rules($supplierPurchaseOrder->id));
        $supplierPurchaseOrder->update($data);

        return $supplierPurchaseOrder;
    }

    public function destroy(SupplierPurchaseOrder $supplierPurchaseOrder)
    {
        $supplierPurchaseOrder->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'purchase_order_number' => ['required', 'string', 'max:255', Rule::unique('supplier_purchase_orders', 'purchase_order_number')->ignore($ignoreId)],
            'purchase_requisition_id' => ['required', 'exists:purchase_requisitions,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'quotation_id' => ['required', 'exists:supplier_quotations,id'],
            'po_date' => ['required', 'date'],
            'delivery_date' => ['required', 'date'],
            'livestock_type' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
            'estimated_weight_kg' => ['required', 'numeric', 'min:0'],
            'unit_rate' => ['required', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'terms_and_conditions' => ['nullable', 'string'],
            'supplier_approval_status' => ['required', Rule::in(['Pending', 'Accepted', 'Declined'])],
            'purchase_order_status' => ['required', Rule::in(['Draft', 'Sent to Supplier', 'Confirmed', 'Completed', 'Cancelled'])],
        ];
    }
}
