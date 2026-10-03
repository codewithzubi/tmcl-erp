<?php

namespace App\Http\Controllers;

use App\Models\OffalCollector;
use Illuminate\Http\Request;

class OffalCollectorController extends Controller
{
    public function index()
    {
        return OffalCollector::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return OffalCollector::create($data);
    }

    public function show(OffalCollector $offalCollector)
    {
        return $offalCollector;
    }

    public function update(Request $request, OffalCollector $offalCollector)
    {
        $data = $request->validate($this->rules());
        $offalCollector->update($data);

        return $offalCollector;
    }

    public function destroy(OffalCollector $offalCollector)
    {
        $offalCollector->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
