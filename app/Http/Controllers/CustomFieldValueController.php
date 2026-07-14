<?php

namespace App\Http\Controllers;

use App\Models\CustomFieldDefinition;
use App\Models\CustomFieldValue;
use Illuminate\Http\Request;

class CustomFieldValueController extends Controller
{
    // Returns every active field definition for the module, each carrying
    // its current value for the given record (or null if unset).
    public function index(Request $request)
    {
        $request->validate([
            'module' => ['required', 'string'],
            'record_id' => ['required', 'integer'],
        ]);

        return CustomFieldDefinition::where('module', $request->module)
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->with(['values' => fn ($q) => $q->where('record_id', $request->record_id)])
            ->get()
            ->map(fn ($def) => [
                'id' => $def->id,
                'field_key' => $def->field_key,
                'label' => $def->label,
                'field_type' => $def->field_type,
                'options' => $def->options,
                'required' => $def->required,
                'value' => $def->values->first()?->value,
            ]);
    }

    // Bulk upsert: { module, record_id, values: { field_key: value, ... } }
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'module' => ['required', 'string'],
            'record_id' => ['required', 'integer'],
            'values' => ['required', 'array'],
        ]);

        $definitions = CustomFieldDefinition::where('module', $data['module'])
            ->whereIn('field_key', array_keys($data['values']))
            ->get()
            ->keyBy('field_key');

        foreach ($data['values'] as $fieldKey => $value) {
            $definition = $definitions->get($fieldKey);
            if (! $definition) {
                continue;
            }

            CustomFieldValue::updateOrCreate(
                ['custom_field_definition_id' => $definition->id, 'record_id' => $data['record_id']],
                ['value' => $value]
            );
        }

        return response()->noContent();
    }
}
