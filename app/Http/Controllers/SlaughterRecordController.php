<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsEvents;
use App\Models\SlaughterRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SlaughterRecordController extends Controller
{
    use LogsEvents;

    public function index(Request $request)
    {
        return SlaughterRecord::with('lot')
            ->when($request->filled('lot_id'), fn ($q) => $q->where('lot_id', $request->lot_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        if (empty($data['animal_code']) || empty($data['animal_sequence_number'])) {
            if (!empty($data['lot_id'])) {
                $lot = \App\Models\Lot::find($data['lot_id']);
                $seq = SlaughterRecord::where('lot_id', $data['lot_id'])->count() + 1;
                $data['animal_sequence_number'] = $seq;
                $data['animal_code'] ??= $lot->lot_code.'-'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
            } else {
                $seq = SlaughterRecord::count() + 1;
                $data['animal_sequence_number'] = $seq;
                $data['animal_code'] ??= 'SLT-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            }
        }

        $record = SlaughterRecord::create($data);
        $this->logEvent('Slaughter', 'Slaughter', $record->id, 'Create', $record->animal_code);

        return $record;
    }

    public function show(SlaughterRecord $slaughterRecord)
    {
        return $slaughterRecord->load(
            'lot', 'offalRecoveries', 'carcassWeightRecords',
            'veterinaryInspections', 'meatDeductions', 'bonelessRecord', 'botiRecord'
        );
    }

    public function update(Request $request, SlaughterRecord $slaughterRecord)
    {
        $data = $request->validate($this->rules($slaughterRecord->id));
        $slaughterRecord->update($data);
        $this->logEvent('Slaughter', 'Slaughter', $slaughterRecord->id, 'Update', json_encode($data));

        return $slaughterRecord;
    }

    public function destroy(SlaughterRecord $slaughterRecord)
    {
        $this->logEvent('Slaughter', 'Slaughter', $slaughterRecord->id, 'Delete', $slaughterRecord->animal_code);
        $slaughterRecord->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'animal_code' => ['nullable', 'string', 'max:255', Rule::unique('slaughter_records', 'animal_code')->ignore($ignoreId)],
            'lot_id' => ['nullable', 'exists:lots,id'],
            'sales_order_number' => ['nullable', 'string', 'max:255'],
            'animal_sequence_number' => ['nullable', 'integer', 'min:1'],
            'slaughter_date' => ['required', 'date'],
            'start_datetime' => ['nullable', 'date'],
            'slaughter_operator' => ['required', 'string', 'max:255'],
            'processing_status' => ['required', Rule::in(['In Progress', 'Completed'])],
            'remarks' => ['nullable', 'string'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'agent' => ['nullable', 'string', 'max:255'],
            'doctor' => ['nullable', 'string', 'max:255'],
            'meat_checker' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'final_product' => ['nullable', 'string', 'max:255'],
            'planned_chiller' => ['nullable', 'string', 'max:255'],
            'belt_attachment' => ['nullable', 'string', 'max:255'],
            'carcass_type' => ['nullable', 'string', 'max:255'],
            'teeth' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:255'],
            'specie' => ['nullable', 'string', 'max:255'],
            'attachment_path' => ['nullable', 'string', 'max:255'],
            'end_slaughter_at' => ['nullable', 'date'],
            'rejection_weight' => ['nullable', 'numeric', 'min:0'],
            'final_weight' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
