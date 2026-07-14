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
            'customer_type' => ['required', Rule::in(['Local', 'International'])],
            'company_name' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'string', 'max:50'],
            'landline' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:255'],
            'industry_type' => ['required', 'string', 'max:255'],
            'customer_category' => ['required', 'string', 'max:255'],
            'tax_registration_number' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:10'],
            'payment_terms' => ['required', 'string', 'max:255'],
            'billing_address' => ['required', 'string'],
            'shipping_address' => ['required', 'string'],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
