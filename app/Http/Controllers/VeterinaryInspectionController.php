<?php

namespace App\Http\Controllers;

use App\Models\VeterinaryInspection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VeterinaryInspectionController extends Controller
{
    public function index(Request $request)
    {
        return VeterinaryInspection::with('slaughterRecord')
            ->when($request->filled('slaughter_record_id'), fn ($q) => $q->where('slaughter_record_id', $request->slaughter_record_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['inspection_number'] ??= 'VI-'.str_pad((string) (VeterinaryInspection::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return VeterinaryInspection::create($data);
    }

    public function show(VeterinaryInspection $veterinaryInspection)
    {
        return $veterinaryInspection->load('slaughterRecord');
    }

    public function update(Request $request, VeterinaryInspection $veterinaryInspection)
    {
        $data = $request->validate($this->rules($veterinaryInspection->id));
        $veterinaryInspection->update($data);

        return $veterinaryInspection;
    }

    public function destroy(VeterinaryInspection $veterinaryInspection)
    {
        $veterinaryInspection->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'inspection_number' => ['nullable', 'string', 'max:255', Rule::unique('veterinary_inspections', 'inspection_number')->ignore($ignoreId)],
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'doctor' => ['required', 'string', 'max:255'],
            'inspection_date' => ['required', 'date'],
            'inspection_result' => ['required', Rule::in(['Approved', 'Partial Reject', 'Full Reject'])],
            'disease_observation' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
