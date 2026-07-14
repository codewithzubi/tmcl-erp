<?php

namespace App\Http\Controllers;

use App\Models\OffalRecovery;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OffalRecoveryController extends Controller
{
    public function index(Request $request)
    {
        return OffalRecovery::with('slaughterRecord')
            ->when($request->filled('slaughter_record_id'), fn ($q) => $q->where('slaughter_record_id', $request->slaughter_record_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['recovery_number'] ??= 'OR-'.str_pad((string) (OffalRecovery::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return OffalRecovery::create($data);
    }

    public function show(OffalRecovery $offalRecovery)
    {
        return $offalRecovery->load('slaughterRecord');
    }

    public function update(Request $request, OffalRecovery $offalRecovery)
    {
        $data = $request->validate($this->rules($offalRecovery->id));
        $offalRecovery->update($data);

        return $offalRecovery;
    }

    public function destroy(OffalRecovery $offalRecovery)
    {
        $offalRecovery->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'recovery_number' => ['nullable', 'string', 'max:255', Rule::unique('offal_recoveries', 'recovery_number')->ignore($ignoreId)],
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'recovery_date' => ['required', 'date'],
            'recovery_type' => ['required', Rule::in(['Offal', 'Fat', 'Hide/Skin', 'Waste', 'Other'])],
            'measured_weight' => ['required', 'numeric', 'min:0'],
            'recorded_by' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
