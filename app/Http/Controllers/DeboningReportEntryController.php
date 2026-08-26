<?php

namespace App\Http\Controllers;

use App\Models\DeboningReportEntry;
use Illuminate\Http\Request;

class DeboningReportEntryController extends Controller
{
    public function index()
    {
        return DeboningReportEntry::with('bonelessProcessingRecord')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return DeboningReportEntry::create($data)->load('bonelessProcessingRecord');
    }

    public function update(Request $request, DeboningReportEntry $deboningReportEntry)
    {
        $data = $request->validate($this->rules());
        $deboningReportEntry->update($data);

        return $deboningReportEntry->load('bonelessProcessingRecord');
    }

    public function destroy(DeboningReportEntry $deboningReportEntry)
    {
        $deboningReportEntry->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'boneless_processing_record_id' => ['required', 'exists:boneless_processing_records,id'],
            'production_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'no_of_animals' => ['nullable', 'numeric', 'min:0'],
            'cut_breakdown' => ['nullable', 'array'],
            'cut_breakdown.*.name' => ['required_with:cut_breakdown', 'string', 'max:255'],
            'cut_breakdown.*.cartons' => ['nullable', 'numeric', 'min:0'],
            'cut_breakdown.*.net_weight' => ['nullable', 'numeric', 'min:0'],
            'new_balance_boneless' => ['nullable', 'numeric', 'min:0'],
            'old_balance_boneless_used' => ['nullable', 'numeric', 'min:0'],
            'send_for_other_party' => ['nullable', 'numeric', 'min:0'],
            'trimming' => ['nullable', 'numeric', 'min:0'],
            'rejected_flank' => ['nullable', 'numeric', 'min:0'],
            'rejected_meat' => ['nullable', 'numeric', 'min:0'],
            'wastage' => ['nullable', 'numeric', 'min:0'],
            'tendon' => ['nullable', 'numeric', 'min:0'],
            'boneless_boti' => ['nullable', 'numeric', 'min:0'],
            'kitchen_issued' => ['nullable', 'numeric', 'min:0'],
            'bone_issued' => ['nullable', 'numeric', 'min:0'],
            'nalli_issued' => ['nullable', 'numeric', 'min:0'],
            'fat_issued' => ['nullable', 'numeric', 'min:0'],
            'irani_dr_vet_code' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
