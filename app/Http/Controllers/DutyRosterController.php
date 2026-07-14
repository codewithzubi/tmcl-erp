<?php

namespace App\Http\Controllers;

use App\Models\DutyRoster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DutyRosterController extends Controller
{
    public function index(Request $request)
    {
        return DutyRoster::with(['user', 'shift'])
            ->when($request->filled('duty_date'), fn ($q) => $q->where('duty_date', $request->duty_date))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->orderByDesc('duty_date')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return DutyRoster::create($data)->load(['user', 'shift']);
    }

    public function show(DutyRoster $dutyRoster)
    {
        return $dutyRoster->load(['user', 'shift']);
    }

    public function update(Request $request, DutyRoster $dutyRoster)
    {
        $data = $request->validate($this->rules($dutyRoster->id));
        $dutyRoster->update($data);

        return $dutyRoster->load(['user', 'shift']);
    }

    public function destroy(DutyRoster $dutyRoster)
    {
        $dutyRoster->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'shift_id' => ['required', 'exists:shifts,id'],
            'duty_date' => ['required', 'date'],
            'department' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Scheduled', 'Completed', 'Absent'])],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
