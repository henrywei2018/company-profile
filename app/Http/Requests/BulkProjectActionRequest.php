<?php
// File: app/Http/Requests/BulkProjectActionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class BulkProjectActionRequest extends FormRequest
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
        // Build available actions based on existing columns
        $availableActions = ['feature', 'unfeature', 'delete'];
        
        if ($this->hasColumn('is_active')) {
            $availableActions[] = 'activate';
            $availableActions[] = 'deactivate';
        }
        
        if ($this->hasColumn('status')) {
            $availableActions[] = 'change_status';
        }
        
        if ($this->hasColumn('priority')) {
            $availableActions[] = 'change_priority';
        }

        $rules = [
            'action' => ['required', Rule::in($availableActions)],
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
        ];

        // Add conditional rules only if columns exist and action requires them
        if ($this->hasColumn('status')) {
            $rules['status'] = ['required_if:action,change_status', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])];
        }
        
        if ($this->hasColumn('priority')) {
            $rules['priority'] = ['required_if:action,change_priority', Rule::in(['low', 'normal', 'high', 'urgent'])];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        $messages = [
            'action.required' => 'Please select an action to perform.',
            'action.in' => 'Invalid action selected.',
            'project_ids.required' => 'Please select at least one project.',
            'project_ids.min' => 'Please select at least one project.',
            'project_ids.*.exists' => 'One or more selected projects do not exist.',
        ];

        // Add conditional messages only if columns exist
        if ($this->hasColumn('status')) {
            $messages['status.required_if'] = 'Status is required when changing project status.';
            $messages['status.in'] = 'Invalid status selected.';
        }
        
        if ($this->hasColumn('priority')) {
            $messages['priority.required_if'] = 'Priority is required when changing project priority.';
            $messages['priority.in'] = 'Invalid priority selected.';
        }

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'project_ids' => 'projects',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Ensure project_ids is an array
        if (isset($input['project_ids']) && !is_array($input['project_ids'])) {
            $input['project_ids'] = [$input['project_ids']];
        }

        // Convert empty strings to null for optional fields
        $nullableFields = [];
        
        if ($this->hasColumn('status')) {
            $nullableFields[] = 'status';
        }
        if ($this->hasColumn('priority')) {
            $nullableFields[] = 'priority';
        }

        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $input) && $input[$field] === '') {
                $input[$field] = null;
            }
        }

        $this->replace($input);
    }

    /**
     * Handle additional validation for business rules.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that selected action is compatible with available columns
            $action = $this->input('action');
            
            if (in_array($action, ['activate', 'deactivate']) && !$this->hasColumn('is_active')) {
                $validator->errors()->add(
                    'action',
                    'Cannot perform activate/deactivate actions: is_active column not available.'
                );
            }
            
            if ($action === 'change_status' && !$this->hasColumn('status')) {
                $validator->errors()->add(
                    'action',
                    'Cannot change status: status column not available.'
                );
            }
            
            if ($action === 'change_priority' && !$this->hasColumn('priority')) {
                $validator->errors()->add(
                    'action',
                    'Cannot change priority: priority column not available.'
                );
            }

            // Validate that required fields are provided for specific actions
            if ($action === 'change_status' && $this->hasColumn('status') && !$this->filled('status')) {
                $validator->errors()->add(
                    'status',
                    'Status is required when changing project status.'
                );
            }
            
            if ($action === 'change_priority' && $this->hasColumn('priority') && !$this->filled('priority')) {
                $validator->errors()->add(
                    'priority',
                    'Priority is required when changing project priority.'
                );
            }
        });
    }
}