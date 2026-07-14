<?php

namespace App\Http\Controllers;

use App\Models\BotiProcessingRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BotiProcessingRecordController extends Controller
{
    public function index(Request $request)
    {
        return BotiProcessingRecord::with('lot', 'slaughterRecord')
            ->when($request->filled('lot_id'), fn ($q) => $q->where('lot_id', $request->lot_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['processing_number'] ??= 'BOTI-'.str_pad((string) (BotiProcessingRecord::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return BotiProcessingRecord::create($data);
    }

    public function show(BotiProcessingRecord $botiProcessingRecord)
    {
        return $botiProcessingRecord->load('lot', 'slaughterRecord');
    }

    public function update(Request $request, BotiProcessingRecord $botiProcessingRecord)
    {
        $data = $request->validate($this->rules($botiProcessingRecord->id));
        $botiProcessingRecord->update($data);

        return $botiProcessingRecord;
    }

    public function destroy(BotiProcessingRecord $botiProcessingRecord)
    {
        $botiProcessingRecord->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'processing_number' => ['nullable', 'string', 'max:255', Rule::unique('boti_processing_records', 'processing_number')->ignore($ignoreId)],
            'lot_id' => ['required', 'exists:lots,id'],
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'processing_date' => ['required', 'date'],
            'input_weight' => ['required', 'numeric', 'min:0'],
            'boti_weight' => ['required', 'numeric', 'min:0'],
            'bone_weight' => ['required', 'numeric', 'min:0'],
            'operator' => ['required', 'string', 'max:255'],
        ];
    }
}
