<?php

namespace App\Http\Controllers;

use App\Models\GateEntryAttachment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GateEntryAttachmentController extends Controller
{
    public function index(Request $request)
    {
        return GateEntryAttachment::query()
            ->when($request->filled('gate_entry_id'), fn ($q) => $q->where('gate_entry_id', $request->gate_entry_id))
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'gate_entry_id' => ['required', 'exists:gate_entries,id'],
            'slot' => ['required', Rule::in(['entry_photograph', 'driver_cnic_copy', 'vehicle_documents', 'additional'])],
            'file_name' => ['required', 'string', 'max:255'],
            'file_type' => ['required', 'string', 'max:100'],
            'size_kb' => ['required', 'integer', 'min:0'],
        ]);

        return GateEntryAttachment::create($data);
    }

    public function destroy(GateEntryAttachment $gateEntryAttachment)
    {
        $gateEntryAttachment->delete();

        return response()->noContent();
    }
}
