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
                // MAX(), not count() — count() drifts below the highest number
                // ever assigned once any record has been deleted, producing a
                // duplicate/already-used code instead of the true next one.
                $seq = (SlaughterRecord::where('lot_id', $data['lot_id'])->max('animal_sequence_number') ?? 0) + 1;
                $data['animal_sequence_number'] = $seq;
                $data['animal_code'] ??= $lot->lot_code.'-'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
            } else {
                $seq = (SlaughterRecord::max('animal_sequence_number') ?? 0) + 1;
                $data['animal_sequence_number'] = $seq;
                // DATE/BELT/SERIAL.NO — e.g. "16-sep-26/Belt-1/001". Serial
                // keeps incrementing globally (not reset per day/belt), same
                // as the plain sequence number this format replaced.
                $date = \Carbon\Carbon::parse($data['slaughter_date'])->format('d-M-y');
                $belt = $data['belt_attachment'] ?? 'NOBELT';
                $serial = str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
                $data['animal_code'] ??= strtolower($date).'/'.$belt.'/'.$serial;
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
        try {
            $slaughterRecord->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ((int) $e->getCode() === 23000) {
                abort(409, 'This animal has Boneless/Boti processing records linked to it and cannot be deleted. Delete those records first.');
            }
            throw $e;
        }

        $this->logEvent('Slaughter', 'Slaughter', $slaughterRecord->id, 'Delete', $slaughterRecord->animal_code);

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'animal_code' => ['nullable', 'string', 'max:255', Rule::unique('slaughter_records', 'animal_code')->ignore($ignoreId)],
            'lot_id' => ['nullable', 'exists:lots,id'],
            'sales_order_number' => ['nullable', 'string', 'max:255'],
            'animal_sequence_number' => ['nullable', 'integer', 'min:1'],
            'slaughter_date' => ['nullable', 'date'],
            'start_datetime' => ['nullable', 'date'],
            'slaughter_operator' => ['nullable', 'string', 'max:255'],
            'processing_status' => ['nullable', Rule::in(['In Progress', 'Completed'])],
            'remarks' => ['nullable', 'string'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'supplier_ids' => ['nullable', 'array'],
            'supplier_ids.*' => ['integer', 'exists:suppliers,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_ids' => ['nullable', 'array'],
            'customer_ids.*' => ['integer', 'exists:customers,id'],
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
            'breed' => ['nullable', 'string', 'max:255'],
            'attachment_path' => ['nullable', 'string', 'max:255'],
            'attachment_type' => ['nullable', 'string', 'max:100'],
            'attachment_data' => ['nullable', 'string'],
            'attachment_title' => ['nullable', 'string', 'max:255'],
            'attachment_description' => ['nullable', 'string'],
            'end_slaughter_at' => ['nullable', 'date'],
            'reopened_at' => ['nullable', 'date'],
            'reopened_by' => ['nullable', 'string', 'max:255'],
            'rejection_weight' => ['nullable', 'numeric', 'min:0'],
            'final_weight' => ['nullable', 'numeric', 'min:0'],
            'custom_adjustments' => ['nullable', 'array'],
            'custom_adjustments.*.title' => ['nullable', 'string', 'max:255'],
            'custom_adjustments.*.amount' => ['nullable', 'numeric'],
            // Also camelCase, same reason as rejected_pieces_entries.* below.
            'custom_adjustments.*.supplierId' => ['nullable', 'string', 'max:255'],
            'rejected_pieces_entries' => ['nullable', 'array'],
            'rejected_pieces_entries.*.title' => ['nullable', 'string', 'max:255'],
            'rejected_pieces_entries.*.pieces' => ['nullable', 'integer', 'min:0'],
            'rejected_pieces_entries.*.weight' => ['nullable', 'numeric', 'min:0'],
            // Note: this blob's sub-fields are sent camelCase, unlike most of
            // the API, since rejected_pieces_entries is passed straight
            // through from the frontend's own object shape (see
            // slaughterRecordToApi) rather than being remapped key-by-key.
            'rejected_pieces_entries.*.reweighedWeight' => ['nullable', 'numeric', 'min:0'],
            'rejected_pieces_entries.*.supplierId' => ['nullable', 'string', 'max:255'],
            'offal_entries' => ['nullable', 'array'],
            'offal_entries.*.supplier_id' => ['nullable', 'exists:suppliers,id'],
            'offal_entries.*.pieces' => ['nullable', 'integer', 'min:0'],
            'offal_entries.*.offal_collector_id' => ['nullable', 'exists:offal_collectors,id'],
            'offal_entries.*.comments' => ['nullable', 'string'],
            'rejected_piece_ids' => ['nullable', 'array'],
            'rejected_piece_ids.*' => ['string', 'max:255'],
            'supplier_rejection_recoveries' => ['nullable', 'array'],
            're_weight_entries' => ['nullable', 'array'],
            're_weight_entries.*.tag_id' => ['nullable', 'string', 'max:255'],
            're_weight_entries.*.original_weight' => ['nullable', 'numeric', 'min:0'],
            're_weight_entries.*.new_weight' => ['nullable', 'numeric', 'min:0'],
            'chiller_transfers' => ['nullable', 'array'],
            'chiller_transfers.*.chiller_name' => ['nullable', 'string', 'max:255'],
            'chiller_transfers.*.chiller_in_time' => ['nullable', 'string', 'max:255'],
            'chiller_transfers.*.plan_duration_hours' => ['nullable', 'numeric', 'min:0'],
            'blast_freezer_transfers' => ['nullable', 'array'],
            'blast_freezer_transfers.*.blast_freezer_name' => ['nullable', 'string', 'max:255'],
            'blast_freezer_transfers.*.blast_freezer_in_time' => ['nullable', 'string', 'max:255'],
            'blast_freezer_transfers.*.plan_duration_hours' => ['nullable', 'numeric', 'min:0'],
            'chiller_transfer_qty' => ['nullable', 'numeric', 'min:0'],
            'blast_freezer_transfer_qty' => ['nullable', 'numeric', 'min:0'],
            'boti_transfer_qty' => ['nullable', 'numeric', 'min:0'],
            'boneless_transfer_qty' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
