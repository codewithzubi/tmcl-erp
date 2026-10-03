<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        return Customer::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = "%{$request->search}%";
                $q->where(fn ($q2) => $q2->where('company_name', 'like', $term)
                    ->orWhere('customer_name', 'like', $term)
                    ->orWhere('customer_code', 'like', $term));
            })
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['customer_code'] ??= 'CUST-'.str_pad((string) (Customer::max('id') + 1), 4, '0', STR_PAD_LEFT);

        return Customer::create($data);
    }

    public function show(Customer $customer)
    {
        return $customer->load([
            'contactPersons', 'discussionNotes', 'attachments',
            'requirements', 'proposals', 'purchaseOrders', 'salesOrders',
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate($this->rules($customer->id));
        $customer->update($data);

        return $customer;
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'customer_code' => ['nullable', 'string', 'max:255', Rule::unique('customers', 'customer_code')->ignore($ignoreId)],
            'customer_type' => ['nullable', Rule::in(['Local', 'International'])],
            'company_name' => ['nullable', 'string', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'mobile' => ['nullable', 'string', 'max:11', 'regex:/^[0-9]+$/'],
            'landline' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:255'],
            'industry_type' => ['nullable', 'string', 'max:255'],
            'customer_category' => ['nullable', 'string', 'max:255'],
            'tax_registration_number' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:10'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'billing_address' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
