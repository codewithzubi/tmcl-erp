<?php

namespace App\Http\Controllers;

use App\Models\CustomerAttachment;
use Illuminate\Http\Request;

class CustomerAttachmentController extends Controller
{
    public function index(Request $request)
    {
        return CustomerAttachment::query()
            ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
            'size_kb' => ['nullable', 'integer', 'min:0'],
            'file_data' => ['nullable', 'string'],
        ]);

        return CustomerAttachment::create($data);
    }

    public function destroy(CustomerAttachment $customerAttachment)
    {
        $customerAttachment->delete();

        return response()->noContent();
    }
}
