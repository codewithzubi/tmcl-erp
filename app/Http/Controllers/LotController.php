<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LotController extends Controller
{
    public function index(Request $request)
    {
        return Lot::with('supplier', 'customer', 'grn')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        if (empty($data['lot_code'])) {
            $soPrefix = 'SO-0000';
            if (! empty($data['sales_order_id'])) {
                $soPrefix = \App\Models\SalesOrder::find($data['sales_order_id'])?->sales_order_number ?? $soPrefix;
            }
            $data['lot_code'] = $soPrefix.'-LOT-'.str_pad((string) (Lot::max('id') + 1), 3, '0', STR_PAD_LEFT);
        }

        $data['status'] ??= 'Open';

        return Lot::create($data);
    }

    public function show(Lot $lot)
    {
        return $lot->load('supplier', 'customer', 'grn', 'barnAllocation', 'salesOrder', 'slaughterRecords');
    }

    public function update(Request $request, Lot $lot)
    {
        $data = $request->validate($this->rules($lot->id));
        $lot->update($data);

        return $lot;
    }

    public function hold(Request $request, Lot $lot)
    {
        $data = $request->validate([
            'hold_reason' => ['required', 'string'],
            'required_remaining_weight' => ['required', 'numeric', 'min:0'],
            'additional_animals_required' => ['required', 'integer', 'min:0'],
        ]);

        $lot->update([...$data, 'status' => 'Hold']);

        return $lot;
    }

    public function release(Request $request, Lot $lot)
    {
        $lot->update([
            'status' => 'Completed',
            'released_by' => $request->input('released_by', $request->user()?->name),
            'release_date' => now(),
        ]);

        return $lot;
    }

    public function destroy(Lot $lot)
    {
        $lot->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'lot_code' => ['nullable', 'string', 'max:255', Rule::unique('lots', 'lot_code')->ignore($ignoreId)],
            'lot_name' => ['nullable', 'string', 'max:255'],
            'sales_order_id' => ['nullable', 'exists:sales_orders,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'supplier_purchase_order_id' => ['nullable', 'exists:supplier_purchase_orders,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'grn_id' => ['required', 'exists:grns,id'],
            'barn_allocation_id' => ['nullable', 'exists:barn_allocations,id'],
            'batch_number' => ['nullable', 'string', 'max:255'],
            'livestock_type' => ['required', 'string', 'max:255'],
            'number_of_animals' => ['required', 'integer', 'min:0'],
            'total_live_weight' => ['required', 'numeric', 'min:0'],
            'allocation_date' => ['required', 'date'],
            'created_by' => ['required', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['Open', 'Hold', 'Completed'])],
            'remarks' => ['nullable', 'string'],
            'supplier_committed_weight' => ['required', 'numeric', 'min:0'],
        ];
    }
}
