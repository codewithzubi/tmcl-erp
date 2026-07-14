<?php

namespace App\Http\Controllers;

use App\Models\Carton;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartonController extends Controller
{
    public function index(Request $request)
    {
        return Carton::with('lot')
            ->when($request->filled('lot_id'), fn ($q) => $q->where('lot_id', $request->lot_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $seq = Carton::max('id') + 1;
        $data['carton_number'] ??= 'CTN-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        $data['barcode'] ??= 'TOMCL-'.(8800 + $seq).'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

        return Carton::create($data);
    }

    public function show(Carton $carton)
    {
        return $carton->load('lot', 'dispatches');
    }

    public function update(Request $request, Carton $carton)
    {
        $data = $request->validate($this->rules($carton->id));
        $carton->update($data);

        return $carton;
    }

    public function destroy(Carton $carton)
    {
        $carton->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'carton_number' => ['nullable', 'string', 'max:255', Rule::unique('cartons', 'carton_number')->ignore($ignoreId)],
            'lot_id' => ['required', 'exists:lots,id'],
            'number_of_packets' => ['required', 'integer', 'min:0'],
            'carton_weight_kg' => ['required', 'numeric', 'min:0'],
            'packaging_material' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('cartons', 'barcode')->ignore($ignoreId)],
            'label_printed' => ['boolean'],
            'status' => ['required', Rule::in(['Open', 'Sealed', 'Dispatched'])],
        ];
    }
}
