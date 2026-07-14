<?php

namespace App\Http\Controllers;

use App\Models\OffalSettlement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OffalSettlementController extends Controller
{
    public function index(Request $request)
    {
        return OffalSettlement::with('supplier', 'lot')
            ->when($request->filled('lot_id'), fn ($q) => $q->where('lot_id', $request->lot_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data = $this->applyPurchaseAmount($data);

        return OffalSettlement::create($data);
    }

    public function show(OffalSettlement $offalSettlement)
    {
        return $offalSettlement->load('supplier', 'lot');
    }

    public function update(Request $request, OffalSettlement $offalSettlement)
    {
        $data = $request->validate($this->rules());
        $data = $this->applyPurchaseAmount($data);
        $offalSettlement->update($data);

        return $offalSettlement;
    }

    public function destroy(OffalSettlement $offalSettlement)
    {
        $offalSettlement->delete();

        return response()->noContent();
    }

    private function applyPurchaseAmount(array $data): array
    {
        if (($data['disposal_method'] ?? null) === 'Purchase by Company') {
            $data['purchase_amount'] = ($data['purchase_rate'] ?? 0) * $data['total_weight'];
        } else {
            $data['purchase_rate'] = null;
            $data['purchase_amount'] = null;
        }

        return $data;
    }

    private function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'lot_id' => ['required', 'exists:lots,id'],
            'by_product_type' => ['required', 'string', 'max:255'],
            'total_weight' => ['required', 'numeric', 'min:0'],
            'disposal_method' => ['required', Rule::in(['Return to Supplier', 'Purchase by Company'])],
            'purchase_rate' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Pending', 'Approved', 'Paid'])],
        ];
    }
}
