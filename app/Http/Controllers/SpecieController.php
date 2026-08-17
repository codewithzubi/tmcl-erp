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

    public function show(Specie $specie)
    {
        return $specie;
    }

    public function update(Request $request, Specie $specie)
    {
        $data = $request->validate($this->rules());
        $specie->update($data);

        return $specie;
    }

    public function destroy(Specie $specie)
    {
        $specie->delete();

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
