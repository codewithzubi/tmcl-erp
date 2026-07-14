<?php

namespace App\Http\Controllers;

use App\Models\CustomFieldDefinition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomFieldDefinitionController extends Controller
{
    public function index(Request $request)
    {
        return CustomFieldDefinition::when($request->filled('module'), fn ($q) => $q->where('module', $request->module))
            ->orderBy('sort_order')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        return CustomFieldDefinition::create($data);
    }

    public function show(CustomFieldDefinition $customFieldDefinition)
    {
        return $customFieldDefinition;
    }

    public function update(Request $request, CustomFieldDefinition $customFieldDefinition)
    {
        $data = $request->validate($this->rules($customFieldDefinition->id));
        $customFieldDefinition->update($data);

        return $customFieldDefinition;
    }

    public function destroy(CustomFieldDefinition $customFieldDefinition)
    {
        $customFieldDefinition->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'module' => ['required', 'string', 'max:255'],
            'field_key' => [
                'required', 'string', 'max:255',
                Rule::unique('custom_field_definitions', 'field_key')->where(fn ($q) => $q->where('module', request('module')))->ignore($ignoreId),
            ],
            'label' => ['required', 'string', 'max:255'],
            'field_type' => ['required', Rule::in(['text', 'number', 'date', 'select', 'checkbox', 'textarea'])],
            'options' => ['nullable', 'array'],
            'required' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ];
    }
}
