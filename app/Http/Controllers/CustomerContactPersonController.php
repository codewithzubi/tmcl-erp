<?php

namespace App\Http\Controllers;

use App\Models\CustomerContactPerson;
use Illuminate\Http\Request;

class CustomerContactPersonController extends Controller
{
    public function index(Request $request)
    {
        return CustomerContactPerson::query()
            ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'string', 'max:50'],
            'is_primary' => ['boolean'],
        ]);

        return CustomerContactPerson::create($data);
    }

    public function update(Request $request, CustomerContactPerson $customerContactPerson)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'string', 'max:50'],
            'is_primary' => ['boolean'],
        ]);

        $customerContactPerson->update($data);

        return $customerContactPerson;
    }

    public function destroy(CustomerContactPerson $customerContactPerson)
    {
        $customerContactPerson->delete();

        return response()->noContent();
    }
}
