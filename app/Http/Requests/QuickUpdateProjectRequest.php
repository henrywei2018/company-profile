<?php
// File: app/Http/Requests/QuickUpdateProjectRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuickUpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('edit projects');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'priority' => ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Invalid project status selected.',
            'priority.in' => 'Invalid project priority selected.',
            'progress_percentage.integer' => 'Progress percentage must be a number.',
            'progress_percentage.min' => 'Progress percentage cannot be less than 0.',
            'progress_percentage.max' => 'Progress percentage cannot exceed 100.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();
        
        // Remove null/empty values except for boolean fields
        $input = array_filter($input, function($value, $key) {
            if (in_array($key, ['featured', 'is_active'])) {
                return true; // Keep boolean fields
            }
            return $value !== null && $value !== '';
        }, ARRAY_FILTER_USE_BOTH);

        // Handle boolean fields
        if ($this->has('featured')) {
            $input['featured'] = $this->boolean('featured');
        }
        if ($this->has('is_active')) {
            $input['is_active'] = $this->boolean('is_active');
        }

        $this->replace($input);
    }
}