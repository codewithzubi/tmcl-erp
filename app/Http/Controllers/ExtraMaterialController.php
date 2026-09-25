<?php

namespace App\Http\Controllers;

use App\Models\ExtraMaterial;
use Illuminate\Http\Request;

class ExtraMaterialController extends Controller
{
    public function index()
    {
        return ExtraMaterial::orderBy('title')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return ExtraMaterial::create($data);
    }

    public function show(ExtraMaterial $extraMaterial)
    {
        return $extraMaterial;
    }

    public function update(Request $request, ExtraMaterial $extraMaterial)
    {
        $data = $request->validate($this->rules());
        $extraMaterial->update($data);

        return $extraMaterial;
    }

    public function destroy(ExtraMaterial $extraMaterial)
    {
        $extraMaterial->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0'],
        ];
    }
}
