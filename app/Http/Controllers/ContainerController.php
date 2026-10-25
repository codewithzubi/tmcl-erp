<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsEvents;
use App\Models\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

// 20 Oct feedback, point 12 — containers are only ever created as a series
// (generate), then opened/closed; a container can be deleted only while
// nothing has been transferred into it.
class ContainerController extends Controller
{
    use LogsEvents;

    public function index()
    {
        return Container::orderByDesc('container_date')->orderBy('vendor')->orderBy('serial')->get();
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'vendor' => ['required', 'string', 'max:100', 'regex:/^[^\/]+$/'],
            'container_date' => ['required', 'date'],
            'count' => ['required', 'integer', 'min:1', 'max:200'],
            'start_serial' => ['required', 'integer', 'min:1'],
        ]);

        $vendor = strtoupper(trim($data['vendor']));
        $date = Carbon::parse($data['container_date'])->startOfDay();
        $rows = [];
        for ($i = 0; $i < $data['count']; $i++) {
            $serial = $data['start_serial'] + $i;
            $rows[] = [
                'name' => "Container-{$vendor}-{$date->format('j M Y')}-{$serial}",
                'vendor' => $vendor,
                'container_date' => $date->toDateString(),
                'serial' => $serial,
            ];
        }

        $existing = Container::whereIn('name', array_column($rows, 'name'))->pluck('name');
        if ($existing->isNotEmpty()) {
            throw ValidationException::withMessages([
                'start_serial' => 'Already exists: ' . $existing->implode(', '),
            ]);
        }

        $created = DB::transaction(fn () => collect($rows)->map(fn ($row) => Container::create($row)));

        $first = $created->first();
        $last = $created->last();
        $this->logEvent('Container Management', 'Containers', $first->id, 'Create',
            $created->count() > 1 ? "{$first->name} to {$last->name}" : $first->name);

        return response()->json($created, 201);
    }

    public function update(Request $request, Container $container)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['Open', 'Closed'])],
        ]);

        $container->update([
            'status' => $data['status'],
            'closed_at' => $data['status'] === 'Closed' ? now() : null,
        ]);
        $this->logEvent('Container Management', 'Containers', $container->id, 'Update', "{$container->name} {$data['status']}");

        return $container;
    }

    public function destroy(Container $container)
    {
        if ($container->meatTransferEntries()->exists()) {
            abort(409, 'This container already has meat in it and cannot be deleted.');
        }

        $this->logEvent('Container Management', 'Containers', $container->id, 'Delete', $container->name);
        $container->delete();

        return response()->noContent();
    }
}
