<?php

namespace App\Http\Controllers;

use App\Models\SupplierQuotation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierQuotationController extends Controller
{
    public function index(Request $request)
    {
        return SupplierQuotation::with('supplier', 'purchaseRequisition')
            ->when($request->filled('purchase_requisition_id'), fn ($q) => $q->where('purchase_requisition_id', $request->purchase_requisition_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return SupplierQuotation::create($data);
    }

    public function show(SupplierQuotation $supplierQuotation)
    {
        return $supplierQuotation->load('supplier', 'purchaseRequisition');
    }

    public function update(Request $request, SupplierQuotation $supplierQuotation)
    {
        $data = $request->validate($this->rules($supplierQuotation->id));
        $supplierQuotation->update($data);

        return $supplierQuotation;
    }

    public function destroy(SupplierQuotation $supplierQuotation)
    {
        $supplierQuotation->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'quotation_number' => ['required', 'string', 'max:255', Rule::unique('supplier_quotations', 'quotation_number')->ignore($ignoreId)],
            'purchase_requisition_id' => ['required', 'exists:purchase_requisitions,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'quotation_date' => ['required', 'date'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'number_of_animals' => ['required', 'integer', 'min:0'],
            'total_weight_kg' => ['required', 'numeric', 'min:0'],
            'delivery_charges' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['required', 'string', 'max:255'],
            'delivery_schedule' => ['required', 'date'],
            'status' => ['required', Rule::in(['Received', 'Under Review', 'Selected', 'Rejected'])],
        ];
    }
}
