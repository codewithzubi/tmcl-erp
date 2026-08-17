<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsEvents;
use App\Models\ExportEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExportEntryController extends Controller
{
    use LogsEvents;

    public function index(Request $request)
    {
        return ExportEntry::with('slaughterRecord')
            ->when($request->filled('slaughter_record_id'), fn ($q) => $q->where('slaughter_record_id', $request->slaughter_record_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $entry = ExportEntry::create($data);
        $this->logEvent('MeatTransfer', 'ExportEntry', $entry->id, 'Create', "{$entry->export_quantity} kg via {$entry->export_mode} from {$entry->chiller_name}");

        return $entry;
    }

    public function destroy(ExportEntry $exportEntry)
    {
        $this->logEvent('MeatTransfer', 'ExportEntry', $exportEntry->id, 'Delete', "{$exportEntry->export_quantity} kg via {$exportEntry->export_mode} from {$exportEntry->chiller_name}");
        $exportEntry->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'chiller_name' => ['required', 'string', 'max:255'],
            'chiller_out_time' => ['required', 'date'],
            'export_date_time' => ['required', 'date'],
            'export_quantity' => ['required', 'numeric', 'min:0'],
            'destination_country' => ['nullable', 'string', 'max:255'],
            'destination_consignee' => ['nullable', 'string', 'max:255'],
            'customer_buyer' => ['nullable', 'string', 'max:255'],
            'forwarder_name' => ['nullable', 'string', 'max:255'],
            'export_reference' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'export_mode' => ['required', Rule::in(['Air', 'Sea', 'Road'])],
            'mode_details' => ['nullable', 'array'],
        ];
    }
}
