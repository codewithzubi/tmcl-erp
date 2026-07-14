<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'supplier_code', 'supplier_type', 'company_name', 'supplier_name', 'contact_person',
        'cnic_or_registration_no', 'mobile', 'email', 'address', 'city', 'country',
        'tax_registration_number', 'payment_terms', 'bank_details', 'currency', 'status', 'remarks',
    ];

    public function contactPersons()
    {
        return $this->hasMany(SupplierContactPerson::class);
    }

    public function attachments()
    {
        return $this->hasMany(SupplierAttachment::class);
    }

    public function livestockSupplyRecords()
    {
        return $this->hasMany(LivestockSupplyRecord::class);
    }

    public function quotations()
    {
        return $this->hasMany(SupplierQuotation::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(SupplierPurchaseOrder::class);
    }

    public function gateEntries()
    {
        return $this->hasMany(GateEntry::class);
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    public function settlements()
    {
        return $this->hasMany(SupplierSettlement::class);
    }
}
