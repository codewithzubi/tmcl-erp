<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\StorageSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        return Dispatch::with('shipment', 'lot', 'carton')
            ->when($request->filled('shipment_id'), fn ($q) => $q->where('shipment_id', $request->shipment_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['dispatch_number'] ??= 'DSP-'.str_pad((string) (Dispatch::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return DB::transaction(function () use ($data) {
            $dispatch = Dispatch::create($data);

            if ($dispatch->status === 'Dispatched') {
                $dispatch->carton()->update(['status' => 'Dispatched']);
                $this->emptyChillerForLot($dispatch->lot_id);
            }

            return $dispatch;
        });
    }

    public function show(Dispatch $dispatch)
    {
        return $dispatch->load('shipment', 'lot', 'carton');
    }

    public function update(Request $request, Dispatch $dispatch)
    {
        $data = $request->validate($this->rules($dispatch->id));
        $dispatch->update($data);

        if ($dispatch->status === 'Dispatched') {
            $dispatch->carton()->update(['status' => 'Dispatched']);
            $this->emptyChillerForLot($dispatch->lot_id);
        }

        return $dispatch;
    }

    // Mirrors the DocScanner "chiller auto-empty" rule: once a lot's product
    // is dispatched, any chiller session still holding it is closed out.
    private function emptyChillerForLot(int $lotId): void
    {
        StorageSession::where('lot_id', $lotId)
            ->where('status', 'Active')
            ->get()
            ->each(function (StorageSession $session) {
                $session->update(['status' => 'Completed', 'time_out' => now()]);
                $session->storageUnit()->decrement('occupied_kg', $session->product_weight);
            });
    }

    public function destroy(Dispatch $dispatch)
    {
        $dispatch->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'dispatch_number' => ['nullable', 'string', 'max:255', Rule::unique('dispatches', 'dispatch_number')->ignore($ignoreId)],
            'shipment_id' => ['required', 'exists:shipments,id'],
            'lot_id' => ['required', 'exists:lots,id'],
            'carton_id' => ['required', 'exists:cartons,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'total_weight_kg' => ['required', 'numeric', 'min:0'],
            'dispatch_time' => ['required', 'date'],
            'dispatch_officer' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Pending', 'Dispatched'])],
            'ph_level' => ['nullable', 'numeric', 'min:0'],
            'cloth_check' => ['nullable', 'string', 'max:255'],
            'temperature' => ['nullable', 'numeric'],
            'label_check' => ['nullable', 'string', 'max:255'],
        ];
    }
}
