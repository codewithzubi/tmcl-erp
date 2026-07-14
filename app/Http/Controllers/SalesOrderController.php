<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        return SalesOrder::with('customer')
            ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return SalesOrder::create($data);
    }

    public function show(SalesOrder $salesOrder)
    {
        return $salesOrder->load('customer', 'proposal', 'purchaseOrder', 'lots');
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        $data = $request->validate($this->rules($salesOrder->id));
        $salesOrder->update($data);

        return $salesOrder;
    }

    public function destroy(SalesOrder $salesOrder)
    {
        $salesOrder->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'sales_order_number' => ['required', 'string', 'max:255', Rule::unique('sales_orders', 'sales_order_number')->ignore($ignoreId)],
            'customer_id' => ['required', 'exists:customers,id'],
            'linked_proposal_id' => ['nullable', 'exists:proposals,id'],
            'linked_purchase_order_id' => ['nullable', 'exists:customer_purchase_orders,id'],
            'order_date' => ['required', 'date'],
            'order_value' => ['required', 'numeric', 'min:0'],
            'approval_status' => ['required', Rule::in(['Pending', 'Approved', 'Rejected'])],
            'production_status' => ['required', Rule::in(['Not Started', 'In Progress', 'Completed'])],
            'logistics_status' => ['required', 'string', 'max:255'],
            'invoice_status' => ['required', Rule::in(['Not Invoiced', 'Partially Invoiced', 'Invoiced'])],
            'payment_status' => ['required', Rule::in(['Unpaid', 'Partially Paid', 'Paid'])],
            'overall_status' => ['required', Rule::in(['Open', 'In Progress', 'Completed', 'Cancelled'])],
        ];
    }
}
