<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamMemberDepartmentRequest extends FormRequest
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
        $departmentId = $this->route('teamMemberDepartment')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('team_member_departments', 'name')->ignore($departmentId)
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('team_member_departments', 'slug')->ignore($departmentId)
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

        // Force is_active to boolean (preserve existing value if not sent)
        $input['is_active'] = $this->boolean('is_active');

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
            $validated['is_active'] = $this->boolean('is_active');
            
            // Clean sort_order
            if (isset($validated['sort_order']) && empty($validated['sort_order'])) {
                unset($validated['sort_order']);
            }
            
            // Clean slug
            if (isset($validated['slug']) && empty($validated['slug'])) {
                unset($validated['slug']); // Let controller generate from name
            }
        }

        return $validated;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }
}