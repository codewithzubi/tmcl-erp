<?php

namespace App\Http\Controllers;

use App\Models\MeatAllocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeatAllocationController extends Controller
{
    private const PROCESSING_RULES = [
        'Full Carcass' => 'Cold Storage',
        'Boneless' => 'Boneless Department',
        'Boti' => 'Boti Department',
    ];

    public function index(Request $request)
    {
        return MeatAllocation::with('customer', 'salesOrder', 'lot')
            ->when($request->filled('lot_id'), fn ($q) => $q->where('lot_id', $request->lot_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['allocation_number'] ??= 'MA-'.str_pad((string) (MeatAllocation::max('id') + 1), 4, '0', STR_PAD_LEFT);
        $data['destination_department'] = self::PROCESSING_RULES[$data['product_type']];

        return MeatAllocation::create($data);
    }

    public function show(MeatAllocation $meatAllocation)
    {
        return $meatAllocation->load('customer', 'salesOrder', 'lot');
    }

    public function update(Request $request, MeatAllocation $meatAllocation)
    {
        $data = $request->validate($this->rules($meatAllocation->id));
        $data['destination_department'] = self::PROCESSING_RULES[$data['product_type']];
        $meatAllocation->update($data);

        return $meatAllocation;
    }

    public function destroy(MeatAllocation $meatAllocation)
    {
        $meatAllocation->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'allocation_number' => ['nullable', 'string', 'max:255', Rule::unique('meat_allocations', 'allocation_number')->ignore($ignoreId)],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'sales_order_id' => ['nullable', 'exists:sales_orders,id'],
            'lot_id' => ['required', 'exists:lots,id'],
            'product_type' => ['required', Rule::in(['Full Carcass', 'Boneless', 'Boti'])],
            'quantity' => ['required', 'numeric', 'min:0'],
            'allocation_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Pending', 'Routed', 'Completed'])],
        ];
    }
}
