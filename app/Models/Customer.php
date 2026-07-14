<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_code', 'customer_type', 'company_name', 'customer_name', 'contact_person',
        'designation', 'email', 'mobile', 'landline', 'website', 'industry_type',
        'customer_category', 'tax_registration_number', 'currency', 'payment_terms',
        'billing_address', 'shipping_address', 'country', 'city', 'status', 'remarks',
    ];

    public function contactPersons()
    {
        return $this->hasMany(CustomerContactPerson::class);
    }

    public function discussionNotes()
    {
        return $this->hasMany(CustomerDiscussionNote::class);
    }

    public function attachments()
    {
        return $this->hasMany(CustomerAttachment::class);
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(CustomerPurchaseOrder::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }
}
