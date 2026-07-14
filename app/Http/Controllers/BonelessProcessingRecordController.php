<?php

namespace App\Http\Controllers;

use App\Models\BonelessProcessingRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BonelessProcessingRecordController extends Controller
{
    public function index(Request $request)
    {
        return BonelessProcessingRecord::with('lot', 'slaughterRecord')
            ->when($request->filled('lot_id'), fn ($q) => $q->where('lot_id', $request->lot_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['processing_number'] ??= 'BNL-'.str_pad((string) (BonelessProcessingRecord::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return BonelessProcessingRecord::create($data);
    }

    public function show(BonelessProcessingRecord $bonelessProcessingRecord)
    {
        return $bonelessProcessingRecord->load('lot', 'slaughterRecord');
    }

    public function update(Request $request, BonelessProcessingRecord $bonelessProcessingRecord)
    {
        $data = $request->validate($this->rules($bonelessProcessingRecord->id));
        $bonelessProcessingRecord->update($data);

        return $bonelessProcessingRecord;
    }

    public function destroy(BonelessProcessingRecord $bonelessProcessingRecord)
    {
        $bonelessProcessingRecord->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'processing_number' => ['nullable', 'string', 'max:255', Rule::unique('boneless_processing_records', 'processing_number')->ignore($ignoreId)],
            'lot_id' => ['required', 'exists:lots,id'],
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'processing_date' => ['required', 'date'],
            'input_weight' => ['required', 'numeric', 'min:0'],
            'boneless_weight' => ['required', 'numeric', 'min:0'],
            'bone_weight' => ['required', 'numeric', 'min:0'],
            'operator' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['In Progress', 'Completed'])],
        ];
    }
}
