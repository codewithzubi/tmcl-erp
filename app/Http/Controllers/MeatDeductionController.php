<?php

namespace App\Http\Controllers;

use App\Models\MeatDeduction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeatDeductionController extends Controller
{
    public function index(Request $request)
    {
        return MeatDeduction::with('slaughterRecord')
            ->when($request->filled('slaughter_record_id'), fn ($q) => $q->where('slaughter_record_id', $request->slaughter_record_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return MeatDeduction::create($data);
    }

    public function show(MeatDeduction $meatDeduction)
    {
        return $meatDeduction->load('slaughterRecord');
    }

    public function update(Request $request, MeatDeduction $meatDeduction)
    {
        $data = $request->validate($this->rules());
        $meatDeduction->update($data);

        return $meatDeduction;
    }

    public function destroy(MeatDeduction $meatDeduction)
    {
        $meatDeduction->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'slaughter_record_id' => ['required', 'exists:slaughter_records,id'],
            'deduction_type' => ['required', Rule::in(['Partial', 'Full'])],
            'rejected_portion' => ['required', Rule::in(['Fore Quarter', 'Hind Quarter', 'Full Carcass', 'Other'])],
            'rejected_weight' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'approved_by' => ['required', 'string', 'max:255'],
        ];
    }
}
