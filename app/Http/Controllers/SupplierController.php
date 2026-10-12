<?php

namespace App\Http\Controllers;

use App\Models\CarcassWeightRecord;
use App\Models\SlaughterRecord;
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
        $data['company_name'] ??= $data['supplier_name'];
        $data['city'] ??= 'N/A';
        $data['country'] ??= 'N/A';
        $data['currency'] ??= 'PKR';
        $data['status'] ??= 'Active';

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
        if ($count = $this->linkedRecordCount($supplier)) {
            return response()->json([
                'message' => "Cannot delete this supplier — {$count} Slaughter/Carcass Weight record(s) already have tags assigned to it. Reassign or remove those first.",
            ], 422);
        }

        $supplier->delete();

        return response()->noContent();
    }

    // Screenshot 21 — a supplier already referenced by production data
    // (a Slaughter record's supplier_id/supplier_ids, or a Carcass Weight
    // record's supplier_id/piece_suppliers) must not be deletable, or those
    // records are left pointing at a supplier id that no longer exists —
    // the same kind of orphaned reference that caused the stale
    // company_name/"ABC" issue. piece_suppliers is a JSON object (piece
    // code => supplier id), so it's checked in PHP rather than SQL.
    private function linkedRecordCount(Supplier $supplier): int
    {
        $slaughterCount = SlaughterRecord::where('supplier_id', $supplier->id)
            ->orWhereJsonContains('supplier_ids', $supplier->id)
            ->count();

        $carcassWeightCount = CarcassWeightRecord::where('supplier_id', $supplier->id)
            ->orWhereNotNull('piece_suppliers')
            ->get(['id', 'supplier_id', 'piece_suppliers'])
            ->filter(fn ($c) => $c->supplier_id === $supplier->id
                || in_array($supplier->id, array_values($c->piece_suppliers ?? [])))
            ->count();

        return $slaughterCount + $carcassWeightCount;
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'supplier_code' => ['nullable', 'string', 'max:255', Rule::unique('suppliers', 'supplier_code')->ignore($ignoreId)],
            'supplier_type' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'cnic_or_registration_no' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:11', 'regex:/^[0-9]+$/'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'tax_registration_number' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'bank_details' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
