<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsEvents;
use App\Models\CarcassWeightPiece;
use App\Models\CarcassWeightRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarcassWeightPieceController extends Controller
{
    use LogsEvents;

    public function index(Request $request)
    {
        return CarcassWeightPiece::query()
            ->when($request->filled('carcass_weight_record_id'), fn ($q) => $q->where('carcass_weight_record_id', $request->carcass_weight_record_id))
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'carcass_weight_record_id' => ['required', 'exists:carcass_weight_records,id'],
            'piece_name' => ['required', Rule::in(['Dasti', 'Raan'])],
        ]);

        $record = CarcassWeightRecord::with('slaughterRecord')->findOrFail($data['carcass_weight_record_id']);
        $serialNo = CarcassWeightPiece::where('carcass_weight_record_id', $record->id)
            ->where('piece_name', $data['piece_name'])
            ->max('serial_no') + 1;

        $piece = CarcassWeightPiece::create([
            'carcass_weight_record_id' => $record->id,
            'piece_name' => $data['piece_name'],
            'serial_no' => $serialNo,
            'composite_id' => $this->buildCompositeId($record, $data['piece_name'], $serialNo),
        ]);

        $this->logEvent('Carcass Weight', 'Animal Piece', $piece->id, 'Create');

        return $piece;
    }

    // Changing a piece's type re-derives its serial number (within the new
    // piece_name group) and composite ID — everything else about the piece
    // is inherited from the carcass weight row, so there's nothing else to edit.
    public function update(Request $request, CarcassWeightPiece $carcassWeightPiece)
    {
        $data = $request->validate([
            'piece_name' => ['required', Rule::in(['Dasti', 'Raan'])],
        ]);

        if ($data['piece_name'] !== $carcassWeightPiece->piece_name) {
            $record = $carcassWeightPiece->carcassWeightRecord()->with('slaughterRecord')->firstOrFail();
            $serialNo = CarcassWeightPiece::where('carcass_weight_record_id', $record->id)
                ->where('piece_name', $data['piece_name'])
                ->where('id', '!=', $carcassWeightPiece->id)
                ->max('serial_no') + 1;

            $carcassWeightPiece->update([
                'piece_name' => $data['piece_name'],
                'serial_no' => $serialNo,
                'composite_id' => $this->buildCompositeId($record, $data['piece_name'], $serialNo),
            ]);
        }

        $this->logEvent('Carcass Weight', 'Animal Piece', $carcassWeightPiece->id, 'Update');

        return $carcassWeightPiece;
    }

    public function destroy(CarcassWeightPiece $carcassWeightPiece)
    {
        $this->logEvent('Carcass Weight', 'Animal Piece', $carcassWeightPiece->id, 'Delete');
        $carcassWeightPiece->delete();

        return response()->noContent();
    }

    // "{AnimalSerialNo}/{PieceName}-{SerialNo}/{Gender}/{Specie}/{Teeth}/{Doctor}/{MeatChecker}"
    // — Doctor/Meat Checker come from the Slaughter record this animal was
    // weighed under, everything else mirrors the parent carcass weight row.
    private function buildCompositeId(CarcassWeightRecord $record, string $pieceName, int $serialNo): string
    {
        $animalSerialNo = explode('/', (string) $record->carcass_animal_id)[0] ?? '';
        $slaughter = $record->slaughterRecord;

        $segments = [
            $animalSerialNo,
            sprintf('%s-%02d', $pieceName, $serialNo),
            $record->gender,
            $record->specie,
            $record->teeth,
            $slaughter?->doctor,
            $slaughter?->meat_checker,
        ];

        return implode('/', array_filter($segments, fn ($s) => filled($s)));
    }
}
