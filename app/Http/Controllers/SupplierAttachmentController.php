<?php

namespace App\Http\Controllers;

use App\Models\SupplierAttachment;
use Illuminate\Http\Request;

class SupplierAttachmentController extends Controller
{
    public function index(Request $request)
    {
        return SupplierAttachment::query()
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'file_name' => ['required', 'string', 'max:255'],
            'file_type' => ['required', 'string', 'max:100'],
            'uploaded_by' => ['required', 'string', 'max:255'],
            'size_kb' => ['required', 'integer', 'min:0'],
        ]);

        return SupplierAttachment::create($data);
    }

    public function destroy(SupplierAttachment $supplierAttachment)
    {
        $supplierAttachment->delete();

        return response()->noContent();
    }
}
