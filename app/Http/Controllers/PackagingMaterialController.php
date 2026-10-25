<?php

namespace App\Http\Controllers;

use App\Models\PackagingMaterial;
use Illuminate\Http\Request;

class PackagingMaterialController extends Controller
{
    public function index()
    {
        return PackagingMaterial::orderBy('title')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return PackagingMaterial::create($data);
    }

    public function show(PackagingMaterial $packagingMaterial)
    {
        return $packagingMaterial;
    }

    public function update(Request $request, PackagingMaterial $packagingMaterial)
    {
        $data = $request->validate($this->rules());
        $packagingMaterial->update($data);

        return $packagingMaterial;
    }

    public function destroy(PackagingMaterial $packagingMaterial)
    {
        $packagingMaterial->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
