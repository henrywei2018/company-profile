<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public form, anyone can submit
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Basic client information
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+\-\(\)\s\d]+$/'],
            'company' => ['nullable', 'string', 'max:255'],
            
            // Project details
            'service_id' => ['nullable', 'exists:services,id'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'budget_range' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            
            // Admin fields
            'status' => ['required', Rule::in(['pending', 'reviewed', 'approved', 'rejected'])],
            'priority' => ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'source' => ['nullable', 'string', 'max:50'],
            'client_id' => ['nullable', 'exists:users,id'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'internal_notes' => ['nullable', 'string', 'max:2000'],
            'estimated_cost' => ['nullable', 'string', 'max:255'],
            'estimated_timeline' => ['nullable', 'string', 'max:255'],
            'additional_info' => ['nullable', 'string', 'max:2000'],
            
            // Actions
            'send_notification' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'client name',
            'email' => 'email address',
            'phone' => 'phone number',
            'project_type' => 'project type',
            'requirements' => 'project requirements',
            'start_date' => 'start date',
            'budget_range' => 'budget range',
            'service_id' => 'service',
            'client_id' => 'client',
            'admin_notes' => 'admin notes',
            'internal_notes' => 'internal notes',
            'estimated_cost' => 'estimated cost',
            'estimated_timeline' => 'estimated timeline',
        ];
    }
}

class ConvertToProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create projects');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Project basic info
            'project_title' => ['required', 'string', 'max:255', 'min:3'],
            'project_description' => ['nullable', 'string', 'max:5000'],
            'project_category_id' => ['nullable', 'exists:project_categories,id'],
            'location' => ['nullable', 'string', 'max:255'],
            
            // Timeline and budget
            'start_date' => ['nullable', 'date'],
            'estimated_completion_date' => ['nullable', 'date', 'after:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])],
            
            // Options
            'create_initial_milestone' => ['boolean'],
            'copy_attachments' => ['boolean'],
            'notify_client' => ['boolean'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'project_title.required' => 'Project title is required.',
            'project_title.min' => 'Project title must be at least 3 characters long.',
            'project_category_id.exists' => 'Selected project category is invalid.',
            'estimated_completion_date.after' => 'Estimated completion date must be after the start date.',
            'budget.numeric' => 'Budget must be a valid number.',
            'budget.min' => 'Budget cannot be negative.',
            'priority.in' => 'Invalid priority level selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'project_title' => 'project title',
            'project_description' => 'project description',
            'project_category_id' => 'project category',
            'start_date' => 'start date',
            'estimated_completion_date' => 'estimated completion date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Set default values for checkboxes
        $checkboxFields = ['create_initial_milestone', 'copy_attachments', 'notify_client'];
        foreach ($checkboxFields as $field) {
            if (!isset($input[$field])) {
                $input[$field] = false;
            }
        }

        // Trim text fields
        $textFields = ['project_title', 'project_description', 'location'];
        foreach ($textFields as $field) {
            if (isset($input[$field])) {
                $input[$field] = trim($input[$field]);
            }
        }

        // Set default priority
        if (!isset($input['priority']) || empty($input['priority'])) {
            $input['priority'] = 'normal';
        }

        $this->replace($input);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that the quotation can be converted
            $quotation = $this->route('quotation');
            
            if ($quotation && $quotation->status !== 'approved') {
                $validator->errors()->add('quotation', 'Only approved quotations can be converted to projects.');
            }

            if ($quotation && $quotation->project_created) {
                $validator->errors()->add('quotation', 'This quotation has already been converted to a project.');
            }

            // Validate project title uniqueness
            if ($this->project_title) {
                $slug = \Illuminate\Support\Str::slug($this->project_title);
                $exists = \App\Models\Project::where('slug', $slug)
                    ->orWhere('slug', 'like', $slug . '-%')
                    ->exists();
                
                if ($exists) {
                    session()->flash('info', 'A project with a similar title already exists. The system will automatically generate a unique identifier.');
                }
            }

            // Validate dates logical relationship
            if ($this->start_date && $this->estimated_completion_date) {
                $start = \Carbon\Carbon::parse($this->start_date);
                $end = \Carbon\Carbon::parse($this->estimated_completion_date);
                
                if ($end->diffInDays($start) > 365 * 3) { // More than 3 years
                    $validator->errors()->add('estimated_completion_date', 'Project duration seems unusually long. Please verify the dates.');
                }
                
                if ($end->diffInDays($start) < 1) { // Less than 1 day
                    $validator->errors()->add('estimated_completion_date', 'Project duration should be at least 1 day.');
                }
            }

            // Validate budget reasonableness
            if ($this->budget) {
                if ($this->budget > 1000000000) { // 1 billion
                    $validator->errors()->add('budget', 'Budget amount seems unusually high. Please verify the amount.');
                }
                
                if ($this->budget < 100) { // Less than 100
                    session()->flash('warning', 'The budget amount seems quite low. Please ensure this is correct.');
                }
            }
        });
    }
}

class BulkQuotationActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('edit quotations');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['approve', 'reject', 'delete', 'change_status', 'convert_to_projects'])],
            'quotation_ids' => ['required', 'string'],
            'new_status' => ['required_if:action,change_status', Rule::in(['pending', 'reviewed', 'approved', 'rejected'])],
            'send_notifications' => ['boolean'],
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
            'quotation_ids.required' => 'Please select at least one quotation.',
            'new_status.required_if' => 'Status is required when changing quotation status.',
            'new_status.in' => 'Invalid status selected.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Set default for send_notifications
        if (!isset($input['send_notifications'])) {
            $input['send_notifications'] = false;
        }

        $this->replace($input);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate quotation IDs
            if ($this->quotation_ids) {
                $ids = array_filter(explode(',', $this->quotation_ids));
                
                if (empty($ids)) {
                    $validator->errors()->add('quotation_ids', 'No valid quotation IDs provided.');
                    return;
                }

                // Check if quotations exist
                $existingCount = \App\Models\Quotation::whereIn('id', $ids)->count();
                if ($existingCount !== count($ids)) {
                    $validator->errors()->add('quotation_ids', 'Some selected quotations do not exist.');
                }

                // Validate action-specific requirements
                if ($this->action === 'convert_to_projects') {
                    $eligibleCount = \App\Models\Quotation::whereIn('id', $ids)
                        ->where('status', 'approved')
                        ->where('project_created', false)
                        ->count();
                    
                    if ($eligibleCount === 0) {
                        $validator->errors()->add('quotation_ids', 'No quotations are eligible for conversion to projects. Quotations must be approved and not already converted.');
                    }
                }

                // Check permissions for delete action
                if ($this->action === 'delete') {
                    if (!auth()->user()->can('delete quotations')) {
                        $validator->errors()->add('action', 'You do not have permission to delete quotations.');
                    }

                    // Check if any quotations have been converted to projects
                    $convertedCount = \App\Models\Quotation::whereIn('id', $ids)
                        ->where('project_created', true)
                        ->count();
                    
                    if ($convertedCount > 0) {
                        $validator->errors()->add('quotation_ids', 'Cannot delete quotations that have been converted to projects.');
                    }
                }
            }
        });
    }
}