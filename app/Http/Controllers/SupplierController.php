<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        return Supplier::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = "%{$request->search}%";
                $q->where(fn ($q2) => $q2->where('company_name', 'like', $term)
                    ->orWhere('supplier_name', 'like', $term)
                    ->orWhere('supplier_code', 'like', $term));
            })
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['supplier_code'] ??= 'SUP-'.str_pad((string) (Supplier::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return Supplier::create($data);
    }

    public function show(Supplier $supplier)
    {
        return $supplier->load(['contactPersons', 'attachments', 'livestockSupplyRecords', 'quotations', 'purchaseOrders']);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate($this->rules($supplier->id));
        $supplier->update($data);

        return $supplier;
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'supplier_code' => ['nullable', 'string', 'max:255', Rule::unique('suppliers', 'supplier_code')->ignore($ignoreId)],
            'supplier_type' => ['required', Rule::in(['Individual Farmer', 'Livestock Trader', 'Feedlot/Farm Company'])],
            'company_name' => ['required', 'string', 'max:255'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'cnic_or_registration_no' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'tax_registration_number' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['required', 'string', 'max:255'],
            'bank_details' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'max:10'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
