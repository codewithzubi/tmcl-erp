<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use Illuminate\Http\Request;

class BreedController extends Controller
{
    public function index()
    {
        return Breed::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return Breed::create($data);
    }

    public function show(Breed $breed)
    {
        return $breed;
    }

    public function update(Request $request, Breed $breed)
    {
        $data = $request->validate($this->rules());
        $breed->update($data);

        return $breed;
    }

    public function destroy(Breed $breed)
    {
        $breed->delete();

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
