<?php

namespace App\Http\Controllers;

use App\Models\LoadingReportEntry;
use Illuminate\Http\Request;

class LoadingReportEntryController extends Controller
{
    public function index()
    {
        return LoadingReportEntry::with('exportEntry')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return LoadingReportEntry::create($data)->load('exportEntry');
    }

    public function update(Request $request, LoadingReportEntry $loadingReportEntry)
    {
        $data = $request->validate($this->rules());
        $loadingReportEntry->update($data);

        return $loadingReportEntry->load('exportEntry');
    }

    public function destroy(LoadingReportEntry $loadingReportEntry)
    {
        $loadingReportEntry->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'export_entry_id' => ['required', 'exists:export_entries,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'total_pcs' => ['nullable', 'integer', 'min:0'],
            'hot_weight' => ['nullable', 'numeric', 'min:0'],
            'basket_crtn' => ['nullable', 'integer', 'min:0'],
            'vehicle_no' => ['nullable', 'string', 'max:255'],
            'container_no' => ['nullable', 'string', 'max:255'],
            'seal_no' => ['nullable', 'string', 'max:255'],
            'gate_pass_no' => ['nullable', 'string', 'max:255'],
            'chilling_start_time' => ['nullable', 'date'],
            'chilling_end_time' => ['nullable', 'date'],
            'indent_no' => ['nullable', 'string', 'max:255'],
            'offload_date_time' => ['nullable', 'date'],
            'offload_total_pcs' => ['nullable', 'integer', 'min:0'],
            'offload_total_weight' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
