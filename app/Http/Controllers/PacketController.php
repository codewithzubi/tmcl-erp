<?php

namespace App\Http\Controllers;

use App\Models\Packet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PacketController extends Controller
{
    public function index(Request $request)
    {
        return Packet::with('lot')
            ->when($request->filled('lot_id'), fn ($q) => $q->where('lot_id', $request->lot_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['packet_number'] ??= 'PKT-'.str_pad((string) (Packet::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return Packet::create($data);
    }

    public function show(Packet $packet)
    {
        return $packet->load('lot');
    }

    public function update(Request $request, Packet $packet)
    {
        $data = $request->validate($this->rules($packet->id));
        $packet->update($data);

        return $packet;
    }

    public function destroy(Packet $packet)
    {
        $packet->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'packet_number' => ['nullable', 'string', 'max:255', Rule::unique('packets', 'packet_number')->ignore($ignoreId)],
            'lot_id' => ['required', 'exists:lots,id'],
            'product_type' => ['required', Rule::in(['Full Carcass', 'Boneless', 'Boti'])],
            'packet_size_kg' => ['required', 'numeric', 'min:0'],
            'number_of_packets' => ['required', 'integer', 'min:0'],
            'weight_per_packet_kg' => ['required', 'numeric', 'min:0'],
            'packaging_material' => ['required', 'string', 'max:255'],
            'packed_by' => ['required', 'string', 'max:255'],
            'packing_date' => ['required', 'date'],
        ];
    }
}
