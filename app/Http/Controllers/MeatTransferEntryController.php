<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsEvents;
use App\Models\MeatTransferEntry;
use Illuminate\Http\Request;

class MeatTransferEntryController extends Controller
{
    use LogsEvents;

    public function index(Request $request)
    {
        return MeatTransferEntry::with('slaughterRecord')
            ->when($request->filled('slaughter_record_id'), fn ($q) => $q->where('slaughter_record_id', $request->slaughter_record_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $entry = MeatTransferEntry::create($data);
        $this->logEvent('MeatTransfer', 'MeatTransferEntry', $entry->id, 'Create', "{$entry->quantity} kg from {$entry->chiller_name}");

        return $entry;
    }

    public function destroy(MeatTransferEntry $meatTransferEntry)
    {
        $this->logEvent('MeatTransfer', 'MeatTransferEntry', $meatTransferEntry->id, 'Delete', "{$meatTransferEntry->quantity} kg from {$meatTransferEntry->chiller_name}");
        $meatTransferEntry->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'chiller_name' => ['required', 'string', 'max:255'],
            'chiller_out_time' => ['required', 'date'],
            'transaction_type' => ['required', 'string', 'max:255'],
            'transfer_department' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
        ];
    }
}
