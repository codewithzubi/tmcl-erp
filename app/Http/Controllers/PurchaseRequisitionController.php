<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PurchaseRequisitionController extends Controller
{
    public function index(Request $request)
    {
        return PurchaseRequisition::with('salesOrder')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return PurchaseRequisition::create($data);
    }

    public function show(PurchaseRequisition $purchaseRequisition)
    {
        return $purchaseRequisition->load('salesOrder', 'quotations', 'purchaseOrders');
    }

    public function update(Request $request, PurchaseRequisition $purchaseRequisition)
    {
        $data = $request->validate($this->rules($purchaseRequisition->id));
        $purchaseRequisition->update($data);

        return $purchaseRequisition;
    }

    public function destroy(PurchaseRequisition $purchaseRequisition)
    {
        $purchaseRequisition->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'pr_number' => ['required', 'string', 'max:255', Rule::unique('purchase_requisitions', 'pr_number')->ignore($ignoreId)],
            'pr_date' => ['required', 'date'],
            'linked_sales_order_id' => ['nullable', 'exists:sales_orders,id'],
            'requesting_department' => ['required', 'string', 'max:255'],
            'procurement_officer' => ['required', 'string', 'max:255'],
            'livestock_type' => ['required', 'string', 'max:255'],
            'required_quantity' => ['required', 'integer', 'min:0'],
            'estimated_weight_kg' => ['required', 'numeric', 'min:0'],
            'expected_delivery_date' => ['required', 'date'],
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Urgent'])],
            'remarks' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['Pending Approval', 'Approved', 'Rejected', 'Closed'])],
        ];
    }
}
