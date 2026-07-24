<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsEvents;
use App\Models\CarcassWeightRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarcassWeightRecordController extends Controller
{
    use LogsEvents;

    public function index(Request $request)
    {
        return CarcassWeightRecord::with('slaughterRecord')
            ->when($request->filled('slaughter_record_id'), fn ($q) => $q->where('slaughter_record_id', $request->slaughter_record_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data = $this->applyQuarterSplit($data);

        $record = CarcassWeightRecord::create($data);
        $this->logEvent('Carcass Weight', 'Carcass Weight', $record->id, 'Create');

        return $record;
    }

    public function show(CarcassWeightRecord $carcassWeightRecord)
    {
        return $carcassWeightRecord->load('slaughterRecord');
    }

    // "Locked" no longer blocks editing/deleting — a user who wants to
    // unlock a finalized weight now does so simply by editing it (the Edit
    // form clears the lock as part of saving; see the frontend's
    // handleEditSubmit).
    public function update(Request $request, CarcassWeightRecord $carcassWeightRecord)
    {
        $data = $request->validate($this->rules($carcassWeightRecord->id));
        $data = $this->applyQuarterSplit($data);
        $carcassWeightRecord->update($data);
        $this->logEvent('Carcass Weight', 'Carcass Weight', $carcassWeightRecord->id, 'Update', json_encode($data));

        return $carcassWeightRecord;
    }

    public function destroy(CarcassWeightRecord $carcassWeightRecord)
    {
        $this->logEvent('Carcass Weight', 'Carcass Weight', $carcassWeightRecord->id, 'Delete');
        $carcassWeightRecord->delete();

        return response()->noContent();
    }

    // Dedicated lock/unlock actions, mirroring the Lot hold/release pattern —
    // toggling the lock must work even while the record is locked.
    public function lock(CarcassWeightRecord $carcassWeightRecord)
    {
        $carcassWeightRecord->update(['locked' => true]);
        $this->logEvent('Carcass Weight', 'Carcass Weight', $carcassWeightRecord->id, 'Approve', 'Locked');

        return $carcassWeightRecord;
    }

    public function unlock(CarcassWeightRecord $carcassWeightRecord)
    {
        $carcassWeightRecord->update(['locked' => false]);
        $this->logEvent('Carcass Weight', 'Carcass Weight', $carcassWeightRecord->id, 'Reject', 'Unlocked');

        return $carcassWeightRecord;
    }

    // Approval is its own workflow, independent of the weight-data lock —
    // a locked (finalized) record must still be approvable, so this bypasses
    // update()'s "locked records can't be edited" guard on purpose.
    public function approve(CarcassWeightRecord $carcassWeightRecord)
    {
        $carcassWeightRecord->update(['supervisor_approval' => 'Approved']);
        $this->logEvent('Carcass Weight', 'Carcass Weight', $carcassWeightRecord->id, 'Approve', 'Approved');

        return $carcassWeightRecord;
    }

    // Mirrors the scope document's business rule: hanging weight splits evenly
    // across the four quarters unless a manual override supplies actual values.
    private function applyQuarterSplit(array $data): array
    {
        if (empty($data['manual_override'])) {
            $quarter = round($data['hanging_weight'] / 4, 2);
            $data['left_hind_quarter'] = $quarter;
            $data['right_hind_quarter'] = $quarter;
            $data['left_fore_quarter'] = $quarter;
            $data['right_fore_quarter'] = $quarter;
        }

        $data['final_carcass_weight'] ??= $data['hanging_weight'];

        return $data;
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'carcass_animal_id' => ['nullable', 'string', 'max:255'],
            'hanging_weight' => ['required', 'numeric', 'min:0'],
            'weight_date_time' => ['required', 'date'],
            'scale_id' => ['required', 'string', 'max:255'],
            'left_hind_quarter' => ['nullable', 'numeric', 'min:0'],
            'right_hind_quarter' => ['nullable', 'numeric', 'min:0'],
            'left_fore_quarter' => ['nullable', 'numeric', 'min:0'],
            'right_fore_quarter' => ['nullable', 'numeric', 'min:0'],
            'manual_override' => ['boolean'],
            'supervisor_approval' => ['required', Rule::in(['Pending', 'Approved', 'Rejected'])],
            'final_carcass_weight' => ['nullable', 'numeric', 'min:0'],
            'gender' => ['nullable', 'string', 'max:255'],
            'specie' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'string', 'max:255'],
            'teeth' => ['nullable', 'string', 'max:255'],
            'hook_weight' => ['nullable', 'numeric', 'min:0'],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'locked' => ['boolean'],
        ];
    }
}
