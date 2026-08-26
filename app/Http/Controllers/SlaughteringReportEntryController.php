<?php

namespace App\Http\Controllers;

use App\Models\SlaughteringReportEntry;
use Illuminate\Http\Request;

class SlaughteringReportEntryController extends Controller
{
    public function index()
    {
        return SlaughteringReportEntry::with('slaughterRecord')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return SlaughteringReportEntry::create($data)->load('slaughterRecord');
    }

    public function update(Request $request, SlaughteringReportEntry $slaughteringReportEntry)
    {
        $data = $request->validate($this->rules());
        $slaughteringReportEntry->update($data);

        return $slaughteringReportEntry->load('slaughterRecord');
    }

    public function destroy(SlaughteringReportEntry $slaughteringReportEntry)
    {
        $slaughteringReportEntry->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'dual_pcs' => ['required', 'integer', 'min:0'],
            'quarter_pcs' => ['required', 'integer', 'min:0'],
            'total_gross_weight' => ['required', 'numeric', 'min:0'],
            'live_weight' => ['nullable', 'numeric', 'min:0'],
            'freight' => ['nullable', 'string', 'max:255'],
        ];
    }
}
