<?php

namespace App\Http\Controllers;

use App\Models\CustomerPurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerPurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        return CustomerPurchaseOrder::with('customer', 'proposal')
            ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return CustomerPurchaseOrder::create($data);
    }

    public function show(CustomerPurchaseOrder $customerPurchaseOrder)
    {
        return $customerPurchaseOrder->load('customer', 'proposal', 'salesOrders');
    }

    public function update(Request $request, CustomerPurchaseOrder $customerPurchaseOrder)
    {
        $data = $request->validate($this->rules($customerPurchaseOrder->id));
        $customerPurchaseOrder->update($data);

        return $customerPurchaseOrder;
    }

    public function destroy(CustomerPurchaseOrder $customerPurchaseOrder)
    {
        $customerPurchaseOrder->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'purchase_order_number' => ['required', 'string', 'max:255', Rule::unique('customer_purchase_orders', 'purchase_order_number')->ignore($ignoreId)],
            'customer_id' => ['required', 'exists:customers,id'],
            'linked_proposal_id' => ['nullable', 'exists:proposals,id'],
            'po_date' => ['required', 'date'],
            'delivery_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Pending Review', 'Approved', 'Rejected'])],
            'internal_remarks' => ['nullable', 'string'],
        ];
    }
}
