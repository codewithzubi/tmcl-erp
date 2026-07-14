<?php

namespace App\Http\Controllers;

use App\Models\SupplierContactPerson;
use Illuminate\Http\Request;

class SupplierContactPersonController extends Controller
{
    public function index(Request $request)
    {
        return SupplierContactPerson::query()
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'string', 'max:50'],
            'is_primary' => ['boolean'],
        ]);

        return SupplierContactPerson::create($data);
    }

    public function update(Request $request, SupplierContactPerson $supplierContactPerson)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'string', 'max:50'],
            'is_primary' => ['boolean'],
        ]);

        $supplierContactPerson->update($data);

        return $supplierContactPerson;
    }

    public function destroy(SupplierContactPerson $supplierContactPerson)
    {
        $supplierContactPerson->delete();

        return response()->noContent();
    }
}
