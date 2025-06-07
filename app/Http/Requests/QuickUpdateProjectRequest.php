<?php
// File: app/Http/Requests/QuickUpdateProjectRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class QuickUpdateProjectRequest extends FormRequest
{
    /**
     * Get actual database columns for projects table
     */
    private function getProjectColumns(): array
    {
        static $columns = null;
        
        if ($columns === null) {
            $columns = Schema::getColumnListing('projects');
        }
        
        return $columns;
    }

    /**
     * Check if a column exists in projects table
     */
    private function hasColumn(string $column): bool
    {
        return in_array($column, $this->getProjectColumns());
    }

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
        $rules = [
            // Core fields that can be quickly updated
            'featured' => 'boolean',
        ];

        // Add conditional rules only if columns exist
        if ($this->hasColumn('status')) {
            $rules['status'] = ['nullable', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])];
        }

        if ($this->hasColumn('priority')) {
            $rules['priority'] = ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])];
        }

        if ($this->hasColumn('progress_percentage')) {
            $rules['progress_percentage'] = 'nullable|integer|min:0|max:100';
        }

        if ($this->hasColumn('is_active')) {
            $rules['is_active'] = 'boolean';
        }

        if ($this->hasColumn('display_order')) {
            $rules['display_order'] = 'nullable|integer|min:0';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        $messages = [];

        // Add conditional messages only if columns exist
        if ($this->hasColumn('status')) {
            $messages['status.in'] = 'Invalid project status selected.';
        }

        if ($this->hasColumn('priority')) {
            $messages['priority.in'] = 'Invalid project priority selected.';
        }

        if ($this->hasColumn('progress_percentage')) {
            $messages['progress_percentage.integer'] = 'Progress percentage must be a number.';
            $messages['progress_percentage.min'] = 'Progress percentage cannot be less than 0.';
            $messages['progress_percentage.max'] = 'Progress percentage cannot exceed 100.';
        }

        if ($this->hasColumn('display_order')) {
            $messages['display_order.integer'] = 'Display order must be a number.';
            $messages['display_order.min'] = 'Display order cannot be negative.';
        }

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [];

        // Add conditional attributes only if columns exist
        if ($this->hasColumn('progress_percentage')) {
            $attributes['progress_percentage'] = 'progress percentage';
        }

        if ($this->hasColumn('is_active')) {
            $attributes['is_active'] = 'active status';
        }

        if ($this->hasColumn('display_order')) {
            $attributes['display_order'] = 'display order';
        }

        return $attributes;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Handle boolean fields
        if (array_key_exists('featured', $input)) {
            $input['featured'] = $this->boolean('featured', false);
        }

        if ($this->hasColumn('is_active') && array_key_exists('is_active', $input)) {
            $input['is_active'] = $this->boolean('is_active', true);
        }

        // Convert empty strings to null for optional fields
        $nullableFields = [];
        
        if ($this->hasColumn('status')) {
            $nullableFields[] = 'status';
        }
        if ($this->hasColumn('priority')) {
            $nullableFields[] = 'priority';
        }
        if ($this->hasColumn('progress_percentage')) {
            $nullableFields[] = 'progress_percentage';
        }
        if ($this->hasColumn('display_order')) {
            $nullableFields[] = 'display_order';
        }

        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $input) && $input[$field] === '') {
                $input[$field] = null;
            }
        }

        $this->replace($input);
    }
}