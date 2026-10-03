<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    public function index()
    {
        return Shift::orderBy('start_time')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return Shift::create($data);
    }

    public function show(Shift $shift)
    {
        return $shift;
    }

    public function update(Request $request, Shift $shift)
    {
        $data = $request->validate($this->rules());
        $shift->update($data);

        return $shift;
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'shift_name' => ['nullable', 'string', 'max:255'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
        ];
    }
}
