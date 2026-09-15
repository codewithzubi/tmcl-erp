<?php

namespace App\Http\Controllers;

use App\Models\Specie;
use Illuminate\Http\Request;

class SpecieController extends Controller
{
    public function index()
    {
        return Specie::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return Specie::create($data);
    }

    public function show(Specie $species)
    {
        return $species;
    }

    public function update(Request $request, Specie $species)
    {
        $data = $request->validate($this->rules());
        $species->update($data);

        return $species;
    }

    public function destroy(Specie $species)
    {
        $species->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
        ];
    }
}
