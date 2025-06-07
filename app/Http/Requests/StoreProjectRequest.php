<?php
// File: app/Http/Requests/StoreProjectRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
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
            // Basic Information
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            
            // Relationships
            'client_id' => 'nullable|exists:users,id',
            'client_name' => 'required_without:client_id|string|max:255',
            'category_id' => 'required|exists:project_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            
            // Status & Management
            'status' => ['required', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            
            // Dates
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_completion_date' => 'nullable|date',
            'actual_completion_date' => 'nullable|date',
            
            // Financial
            'value' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0|max:999999999999.99',
            'actual_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            
            // Project Details
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'result' => 'nullable|string',
            'client_feedback' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
            
            // JSON Fields
            'services_used' => 'nullable|array',
            'services_used.*' => 'string|max:255',
            'technologies_used' => 'nullable|array',
            'technologies_used.*' => 'string|max:255',
            'team_members' => 'nullable|array',
            'team_members.*' => 'string|max:255',
            
            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:1000',
            
            // File Uploads
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
            'image_alt_texts' => 'nullable|array',
            'image_alt_texts.*' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required.',
            'title.max' => 'Project title cannot exceed 255 characters.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'description.required' => 'Project description is required.',
            'short_description.max' => 'Short description cannot exceed 500 characters.',
            'client_name.required_without' => 'Client name is required when no client is selected.',
            'category_id.required' => 'Project category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'service_id.exists' => 'Selected service does not exist.',
            'quotation_id.exists' => 'Selected quotation does not exist.',
            'status.required' => 'Project status is required.',
            'status.in' => 'Invalid project status selected.',
            'priority.required' => 'Project priority is required.',
            'priority.in' => 'Invalid project priority selected.',
            'progress_percentage.integer' => 'Progress percentage must be a number.',
            'progress_percentage.min' => 'Progress percentage cannot be less than 0.',
            'progress_percentage.max' => 'Progress percentage cannot exceed 100.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'budget.numeric' => 'Budget must be a valid number.',
            'budget.min' => 'Budget cannot be negative.',
            'budget.max' => 'Budget amount is too large.',
            'actual_cost.numeric' => 'Actual cost must be a valid number.',
            'actual_cost.min' => 'Actual cost cannot be negative.',
            'actual_cost.max' => 'Actual cost amount is too large.',
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.image' => 'File must be an image.',
            'images.*.mimes' => 'Image must be in JPEG, JPG, PNG, or WebP format.',
            'images.*.max' => 'Image size cannot exceed 2MB.',
            'meta_title.max' => 'Meta title cannot exceed 255 characters.',
            'meta_description.max' => 'Meta description cannot exceed 500 characters.',
            'meta_keywords.max' => 'Meta keywords cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'client_id' => 'client',
            'client_name' => 'client name',
            'category_id' => 'category',
            'service_id' => 'service',
            'quotation_id' => 'quotation',
            'progress_percentage' => 'progress percentage',
            'is_active' => 'active status',
            'display_order' => 'display order',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'estimated_completion_date' => 'estimated completion date',
            'actual_completion_date' => 'actual completion date',
            'actual_cost' => 'actual cost',
            'client_feedback' => 'client feedback',
            'lessons_learned' => 'lessons learned',
            'services_used' => 'services used',
            'technologies_used' => 'technologies used',
            'team_members' => 'team members',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for optional fields
        $nullableFields = [
            'slug', 'short_description', 'location', 'year', 'client_id', 'service_id', 
            'quotation_id', 'start_date', 'end_date', 'estimated_completion_date', 
            'actual_completion_date', 'value', 'budget', 'actual_cost', 'challenge', 
            'solution', 'result', 'client_feedback', 'lessons_learned', 'meta_title', 
            'meta_description', 'meta_keywords'
        ];

        $input = $this->all();
        
        foreach ($nullableFields as $field) {
            if (isset($input[$field]) && $input[$field] === '') {
                $input[$field] = null;
            }
        }

        // Ensure boolean fields are properly cast
        $input['featured'] = $this->boolean('featured', false);
        $input['is_active'] = $this->boolean('is_active', true);

        // Filter out empty array values
        foreach (['services_used', 'technologies_used', 'team_members'] as $arrayField) {
            if (isset($input[$arrayField]) && is_array($input[$arrayField])) {
                $input[$arrayField] = array_filter($input[$arrayField], function($value) {
                    return !empty(trim($value));
                });
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
            $project = $this->route('project');
            
            // Check if project can be marked as completed
            if ($this->input('status') === 'completed' && $project) {
                // Validate that all required milestones are completed
                $incompleteMilestones = $project->milestones()
                    ->where('status', '!=', 'completed')
                    ->count();
                    
                if ($incompleteMilestones > 0) {
                    $validator->errors()->add(
                        'status', 
                        "Cannot mark project as completed. {$incompleteMilestones} milestone(s) are still incomplete."
                    );
                }
            }
            
            // Validate budget vs actual cost relationship
            if ($this->filled('budget') && $this->filled('actual_cost')) {
                $budget = floatval($this->input('budget'));
                $actualCost = floatval($this->input('actual_cost'));
                
                if ($actualCost > $budget * 1.5) { // 50% over budget
                    $validator->errors()->add(
                        'actual_cost', 
                        'Actual cost is significantly over budget. Please review and confirm.'
                    );
                }
            }
            
            // Validate end date is reasonable
            if ($this->filled('end_date') && $this->filled('start_date')) {
                $startDate = \Carbon\Carbon::parse($this->input('start_date'));
                $endDate = \Carbon\Carbon::parse($this->input('end_date'));
                
                if ($endDate->diffInDays($startDate) > 365 * 5) { // More than 5 years
                    $validator->errors()->add(
                        'end_date', 
                        'Project duration exceeds 5 years. Please verify the dates are correct.'
                    );
                }
            }
        });
    }
}