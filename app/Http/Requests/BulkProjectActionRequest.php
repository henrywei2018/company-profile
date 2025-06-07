<?php
// File: app/Http/Requests/BulkProjectActionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkProjectActionRequest extends FormRequest
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
            'action' => ['required', Rule::in(['activate', 'deactivate', 'feature', 'unfeature', 'delete', 'change_status', 'change_priority'])],
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
            'status' => ['required_if:action,change_status', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'priority' => ['required_if:action,change_priority', Rule::in(['low', 'normal', 'high', 'urgent'])],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Please select an action to perform.',
            'action.in' => 'Invalid action selected.',
            'project_ids.required' => 'Please select at least one project.',
            'project_ids.min' => 'Please select at least one project.',
            'project_ids.*.exists' => 'One or more selected projects do not exist.',
            'status.required_if' => 'Status is required when changing project status.',
            'status.in' => 'Invalid status selected.',
            'priority.required_if' => 'Priority is required when changing project priority.',
            'priority.in' => 'Invalid priority selected.',
        ];
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

    // Force is_active to boolean (default true if not present)
    $input['is_active'] = $this->boolean('is_active', true);

    // REMOVED: Filter array fields for services_used, technologies_used, team_members
    // since these fields are no longer used

    $this->replace($input);
}
}