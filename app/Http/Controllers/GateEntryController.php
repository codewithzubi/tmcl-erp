<?php

namespace App\Http\Controllers;

use App\Models\GateEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GateEntryController extends Controller
{
    public function index(Request $request)
    {
        return GateEntry::with('supplier')
            ->when($request->filled('approval_status'), fn ($q) => $q->where('approval_status', $request->approval_status))
            ->when($request->filled('entry_type'), fn ($q) => $q->where('entry_type', $request->entry_type))
            ->latest('entry_date_time')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['gate_entry_number'] ??= 'GATE-'.str_pad((string) (GateEntry::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return GateEntry::create($data);
    }

    public function show(GateEntry $gateEntry)
    {
        return $gateEntry->load('supplier', 'attachments');
    }

    public function update(Request $request, GateEntry $gateEntry)
    {
        $data = $request->validate($this->rules($gateEntry->id));
        $gateEntry->update($data);

        return $gateEntry;
    }

    public function destroy(GateEntry $gateEntry)
    {
        $gateEntry->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'gate_entry_number' => ['nullable', 'string', 'max:255', Rule::unique('gate_entries', 'gate_entry_number')->ignore($ignoreId)],
            'entry_date_time' => ['required', 'date'],
            'entry_type' => ['required', Rule::in(['Supplier Delivery', 'Visitor', 'Vehicle Return', 'Other'])],
            'visitor_name' => ['nullable', 'string', 'max:255'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_cnic' => ['required', 'string', 'max:255'],
            'vehicle_registration_number' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['required', Rule::in(['Truck', 'Container Truck', 'Pickup', 'Trailer', 'Other'])],
            'trailer_number' => ['nullable', 'string', 'max:255'],
            'number_of_animals' => ['nullable', 'integer', 'min:0'],
            'estimated_weight' => ['nullable', 'numeric', 'min:0'],
            'security_officer' => ['required', 'string', 'max:255'],
            'purpose_of_visit' => ['required', Rule::in(['Livestock Delivery', 'Inspection', 'Meeting', 'Maintenance', 'Other'])],
            'remarks' => ['nullable', 'string'],
            'approval_status' => ['required', Rule::in(['Pending', 'Approved', 'Rejected'])],
        ];
    }
}
