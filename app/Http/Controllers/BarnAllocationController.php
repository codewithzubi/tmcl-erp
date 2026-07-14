<?php

namespace App\Http\Controllers;

use App\Models\BarnAllocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarnAllocationController extends Controller
{
    public function index(Request $request)
    {
        return BarnAllocation::with('grn')
            ->when($request->filled('grn_id'), fn ($q) => $q->where('grn_id', $request->grn_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['allocation_number'] ??= 'BA-'.str_pad((string) (BarnAllocation::max('id') + 1), 4, '0', STR_PAD_LEFT);
        $data['batch_number'] ??= 'BATCH-'.str_pad((string) (BarnAllocation::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return BarnAllocation::create($data);
    }

    public function show(BarnAllocation $barnAllocation)
    {
        return $barnAllocation->load('grn', 'lots');
    }

    public function update(Request $request, BarnAllocation $barnAllocation)
    {
        $data = $request->validate($this->rules($barnAllocation->id));
        $barnAllocation->update($data);

        return $barnAllocation;
    }

    public function destroy(BarnAllocation $barnAllocation)
    {
        $barnAllocation->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'allocation_number' => ['nullable', 'string', 'max:255', Rule::unique('barn_allocations', 'allocation_number')->ignore($ignoreId)],
            'grn_id' => ['required', 'exists:grns,id'],
            'barn' => ['required', 'string', 'max:255'],
            'batch_number' => ['nullable', 'string', 'max:255', Rule::unique('barn_allocations', 'batch_number')->ignore($ignoreId)],
            'livestock_type' => ['required', 'string', 'max:255'],
            'number_of_animals_allocated' => ['required', 'integer', 'min:0'],
            'total_weight' => ['required', 'numeric', 'min:0'],
            'allocation_date' => ['required', 'date'],
            'supervisor' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'allocation_status' => ['required', Rule::in(['Allocated', 'Moved', 'Completed'])],
        ];
    }
}
