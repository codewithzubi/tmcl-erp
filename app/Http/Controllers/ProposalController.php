<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        return Proposal::with('lineItems')
            ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['proposal_number'] ??= 'PROP-'.str_pad((string) (Proposal::max('id') + 1), 4, '0', STR_PAD_LEFT);
        $lineItems = $data['line_items'] ?? [];
        unset($data['line_items']);

        return DB::transaction(function () use ($data, $lineItems) {
            $proposal = Proposal::create($data);
            $proposal->lineItems()->createMany($lineItems);

            return $proposal->load('lineItems');
        });
    }

    public function show(Proposal $proposal)
    {
        return $proposal->load('lineItems', 'customer');
    }

    public function update(Request $request, Proposal $proposal)
    {
        $data = $request->validate($this->rules($proposal->id));
        $lineItems = $data['line_items'] ?? null;
        unset($data['line_items']);

        return DB::transaction(function () use ($data, $lineItems, $proposal) {
            $proposal->update($data);

            if ($lineItems !== null) {
                $proposal->lineItems()->delete();
                $proposal->lineItems()->createMany($lineItems);
            }

            return $proposal->load('lineItems');
        });
    }

    public function destroy(Proposal $proposal)
    {
        $proposal->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'proposal_number' => ['nullable', 'string', 'max:255', Rule::unique('proposals', 'proposal_number')->ignore($ignoreId)],
            'customer_id' => ['required', 'exists:customers,id'],
            'proposal_date' => ['required', 'date'],
            'valid_until' => ['required', 'date'],
            'currency' => ['required', 'string', 'max:10'],
            'status' => ['required', Rule::in(['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'])],
            'version_number' => ['nullable', 'integer', 'min:1'],
            'prepared_by' => ['required', 'string', 'max:255'],
            'internal_remarks' => ['nullable', 'string'],
            'line_items' => ['nullable', 'array'],
            'line_items.*.product' => ['required_with:line_items', 'string', 'max:255'],
            'line_items.*.description' => ['nullable', 'string'],
            'line_items.*.quantity' => ['required_with:line_items', 'numeric', 'min:0'],
            'line_items.*.unit' => ['required_with:line_items', 'string', 'max:50'],
            'line_items.*.unit_price' => ['required_with:line_items', 'numeric', 'min:0'],
            'line_items.*.discount_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'line_items.*.tax_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'line_items.*.packaging_charges' => ['nullable', 'numeric', 'min:0'],
            'line_items.*.logistics_charges' => ['nullable', 'numeric', 'min:0'],
            'line_items.*.freight_charges' => ['nullable', 'numeric', 'min:0'],
            'line_items.*.insurance_charges' => ['nullable', 'numeric', 'min:0'],
            'line_items.*.other_charges' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
