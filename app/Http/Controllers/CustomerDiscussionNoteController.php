<?php

namespace App\Http\Controllers;

use App\Models\CustomerDiscussionNote;
use Illuminate\Http\Request;

class CustomerDiscussionNoteController extends Controller
{
    public function index(Request $request)
    {
        return CustomerDiscussionNote::query()
            ->when($request->filled('customer_id'), fn ($q) => $q->where('customer_id', $request->customer_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'author' => ['required', 'string', 'max:255'],
            'note' => ['required', 'string'],
        ]);

        return CustomerDiscussionNote::create($data);
    }

    public function destroy(CustomerDiscussionNote $customerDiscussionNote)
    {
        $customerDiscussionNote->delete();

        return response()->noContent();
    }
}
