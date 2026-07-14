<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RequirementController extends Controller
{
    public function index(Request $request)
    {
        return Requirement::with('customer')
            ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['requirement_code'] ??= 'REQ-'.str_pad((string) (Requirement::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return Requirement::create($data);
    }

    public function show(Requirement $requirement)
    {
        return $requirement->load('customer');
    }

    public function update(Request $request, Requirement $requirement)
    {
        $data = $request->validate($this->rules($requirement->id));
        $requirement->update($data);

        return $requirement;
    }

    public function destroy(Requirement $requirement)
    {
        $requirement->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'requirement_code' => ['nullable', 'string', 'max:255', Rule::unique('requirements', 'requirement_code')->ignore($ignoreId)],
            'customer_id' => ['required', 'exists:customers,id'],
            'product_type' => ['required', 'string', 'max:255'],
            'product_specifications' => ['required', 'string'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit_of_measure' => ['required', 'string', 'max:50'],
            'packaging_requirement' => ['required', 'string', 'max:255'],
            'delivery_location' => ['required', 'string', 'max:255'],
            'expected_delivery_date' => ['required', 'date'],
            'additional_notes' => ['nullable', 'string'],
        ];
    }
}
