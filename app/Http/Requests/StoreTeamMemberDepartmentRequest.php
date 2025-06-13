<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeamMemberDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:team_member_departments,name'
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:team_member_departments,slug'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'is_active' => [
                'boolean'
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:99999'
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required.',
            'name.unique' => 'A department with this name already exists.',
            'name.max' => 'Department name cannot exceed 255 characters.',
            'slug.regex' => 'Slug must only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'sort_order.max' => 'Sort order cannot exceed 99999.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'department name',
            'slug' => 'department slug',
            'description' => 'department description',
            'is_active' => 'active status',
            'sort_order' => 'sort order'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Force is_active to boolean (default true if not present)
        $input['is_active'] = $this->boolean('is_active', true);

        // Clean up sort_order
        if (isset($input['sort_order']) && $input['sort_order'] === '') {
            $input['sort_order'] = null;
        }

        // Clean up slug
        if (isset($input['slug']) && trim($input['slug']) === '') {
            $input['slug'] = null;
        }

        $this->replace($input);
    }

    /**
     * Get the validated data from the request with processed values.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // If no specific key requested, process the full array
        if ($key === null) {
            // Ensure is_active is boolean
            $validated['is_active'] = $this->boolean('is_active', true);
            
            // Set default sort_order if not provided
            if (empty($validated['sort_order'])) {
                unset($validated['sort_order']); // Let controller handle this
            }
            
            // Clean slug
            if (empty($validated['slug'])) {
                unset($validated['slug']); // Let controller generate from name
            }
        }

        return $validated;
    }
}