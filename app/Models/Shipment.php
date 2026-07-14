<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'shipment_number', 'customer_id', 'sales_order_id', 'customer_name', 'shipment_method',
        'shipment_date', 'expected_delivery_date', 'destination_country', 'destination_port',
        'shipping_line', 'vessel_or_flight_number', 'container_or_awb_number',
        'freight_forwarder', 'vehicle_number', 'shipment_status',
    ];

    protected $casts = [
        'shipment_date' => 'date',
        'expected_delivery_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }
}
