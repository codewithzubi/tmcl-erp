<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        return Shipment::with('customer', 'salesOrder')
            ->when($request->filled('shipment_status'), fn ($q) => $q->where('shipment_status', $request->shipment_status))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['shipment_number'] ??= 'SHP-'.str_pad((string) (Shipment::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return Shipment::create($data);
    }

    public function show(Shipment $shipment)
    {
        return $shipment->load('customer', 'salesOrder', 'dispatches');
    }

    public function update(Request $request, Shipment $shipment)
    {
        $data = $request->validate($this->rules($shipment->id));
        $shipment->update($data);

        return $shipment;
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'shipment_number' => ['nullable', 'string', 'max:255', Rule::unique('shipments', 'shipment_number')->ignore($ignoreId)],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'sales_order_id' => ['nullable', 'exists:sales_orders,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'shipment_method' => ['required', Rule::in(['Air', 'Sea'])],
            'shipment_date' => ['required', 'date'],
            'expected_delivery_date' => ['required', 'date'],
            'destination_country' => ['required', 'string', 'max:255'],
            'destination_port' => ['required', 'string', 'max:255'],
            'shipping_line' => ['nullable', 'string', 'max:255'],
            'vessel_or_flight_number' => ['nullable', 'string', 'max:255'],
            'container_or_awb_number' => ['nullable', 'string', 'max:255'],
            'freight_forwarder' => ['nullable', 'string', 'max:255'],
            'vehicle_number' => ['nullable', 'string', 'max:255'],
            'shipment_status' => ['required', Rule::in(['Planned', 'In Transit', 'Delivered'])],
        ];
    }
}
