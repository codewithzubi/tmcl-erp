<?php

namespace App\Http\Controllers;

use App\Models\LivestockInspection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LivestockInspectionController extends Controller
{
    public function index(Request $request)
    {
        return LivestockInspection::with('grn')
            ->when($request->filled('grn_id'), fn ($q) => $q->where('grn_id', $request->grn_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['inspection_number'] ??= 'LI-'.str_pad((string) (LivestockInspection::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return LivestockInspection::create($data);
    }

    public function show(LivestockInspection $livestockInspection)
    {
        return $livestockInspection->load('grn');
    }

    public function update(Request $request, LivestockInspection $livestockInspection)
    {
        $data = $request->validate($this->rules($livestockInspection->id));
        $livestockInspection->update($data);

        return $livestockInspection;
    }

    public function destroy(LivestockInspection $livestockInspection)
    {
        $livestockInspection->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'inspection_number' => ['nullable', 'string', 'max:255', Rule::unique('livestock_inspections', 'inspection_number')->ignore($ignoreId)],
            'grn_id' => ['required', 'exists:grns,id'],
            'veterinary_officer' => ['required', 'string', 'max:255'],
            'inspection_date' => ['required', 'date'],
            'animal_health_status' => ['required', Rule::in(['Healthy', 'Sick', 'Injured', 'Under Observation'])],
            'disease_symptoms' => ['nullable', 'string'],
            'physical_condition' => ['required', Rule::in(['Good', 'Fair', 'Poor'])],
            'body_weight_verification' => ['required', 'numeric', 'min:0'],
            'temperature' => ['required', 'numeric'],
            'quarantine_required' => ['boolean'],
            'inspection_remarks' => ['nullable', 'string'],
            'final_decision' => ['required', Rule::in(['Accept', 'Reject'])],
        ];
    }
}
