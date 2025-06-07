<?php
// File: app/Http/Requests/UpdateProjectRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class UpdateProjectRequest extends FormRequest
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
        $projectId = $this->route('project')->id;

        $rules = [
            // Core fields (always present)
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($projectId)],
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'status' => ['required', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'featured' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'result' => 'nullable|string',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
            'image_alt_texts' => 'nullable|array',
            'image_alt_texts.*' => 'nullable|string|max:255',
        ];

        // Add conditional rules only if columns exist
        if ($this->hasColumn('short_description')) {
            $rules['short_description'] = 'nullable|string|max:500';
        }

        if ($this->hasColumn('client_id')) {
            $rules['client_id'] = 'nullable|exists:users,id';
        }

        if ($this->hasColumn('client_name')) {
            $rules['client_name'] = 'required_without:client_id|string|max:255';
        }

        if ($this->hasColumn('category_id')) {
            $rules['category_id'] = 'required|exists:project_categories,id';
        }

        if ($this->hasColumn('service_id')) {
            $rules['service_id'] = 'nullable|exists:services,id';
        }

        if ($this->hasColumn('quotation_id')) {
            $rules['quotation_id'] = 'nullable|exists:quotations,id';
        }

        if ($this->hasColumn('priority')) {
            $rules['priority'] = ['required', Rule::in(['low', 'normal', 'high', 'urgent'])];
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

        if ($this->hasColumn('estimated_completion_date')) {
            $rules['estimated_completion_date'] = 'nullable|date';
        }

        if ($this->hasColumn('actual_completion_date')) {
            $rules['actual_completion_date'] = 'nullable|date';
        }

        if ($this->hasColumn('value')) {
            $rules['value'] = 'nullable|string|max:255';
        }

        if ($this->hasColumn('budget')) {
            $rules['budget'] = 'nullable|numeric|min:0|max:999999999999.99';
        }

        if ($this->hasColumn('actual_cost')) {
            $rules['actual_cost'] = 'nullable|numeric|min:0|max:999999999999.99';
        }

        if ($this->hasColumn('client_feedback')) {
            $rules['client_feedback'] = 'nullable|string';
        }

        if ($this->hasColumn('lessons_learned')) {
            $rules['lessons_learned'] = 'nullable|string';
        }

        if ($this->hasColumn('meta_title')) {
            $rules['meta_title'] = 'nullable|string|max:255';
        }

        if ($this->hasColumn('meta_description')) {
            $rules['meta_description'] = 'nullable|string|max:500';
        }

        if ($this->hasColumn('meta_keywords')) {
            $rules['meta_keywords'] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        $messages = [
            'title.required' => 'Project title is required.',
            'slug.unique' => 'This slug is already taken.',
            'description.required' => 'Project description is required.',
            'status.required' => 'Project status is required.',
            'status.in' => 'Invalid project status selected.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'images.max' => 'Max 10 images allowed.',
            'images.*.mimes' => 'Images must be jpeg, jpg, png, or webp.',
            'images.*.max' => 'Each image must be under 2MB.',
        ];

        // Add conditional messages only if columns exist
        if ($this->hasColumn('short_description')) {
            $messages['short_description.max'] = 'Short description cannot exceed 500 characters.';
        }

        if ($this->hasColumn('client_name')) {
            $messages['client_name.required_without'] = 'Client name is required when no client is selected.';
        }

        if ($this->hasColumn('category_id')) {
            $messages['category_id.required'] = 'Project category is required.';
            $messages['category_id.exists'] = 'Selected category does not exist.';
        }

        if ($this->hasColumn('service_id')) {
            $messages['service_id.exists'] = 'Selected service does not exist.';
        }

        if ($this->hasColumn('quotation_id')) {
            $messages['quotation_id.exists'] = 'Selected quotation does not exist.';
        }

        if ($this->hasColumn('priority')) {
            $messages['priority.required'] = 'Project priority is required.';
            $messages['priority.in'] = 'Invalid project priority selected.';
        }

        if ($this->hasColumn('progress_percentage')) {
            $messages['progress_percentage.integer'] = 'Progress percentage must be a number.';
            $messages['progress_percentage.min'] = 'Progress percentage cannot be less than 0.';
            $messages['progress_percentage.max'] = 'Progress percentage cannot exceed 100.';
        }

        if ($this->hasColumn('budget')) {
            $messages['budget.numeric'] = 'Budget must be numeric.';
            $messages['budget.min'] = 'Budget cannot be negative.';
        }

        if ($this->hasColumn('actual_cost')) {
            $messages['actual_cost.numeric'] = 'Actual cost must be numeric.';
            $messages['actual_cost.min'] = 'Actual cost cannot be negative.';
        }

        if ($this->hasColumn('meta_title')) {
            $messages['meta_title.max'] = 'Meta title cannot exceed 255 characters.';
        }

        if ($this->hasColumn('meta_description')) {
            $messages['meta_description.max'] = 'Meta description cannot exceed 500 characters.';
        }

        if ($this->hasColumn('meta_keywords')) {
            $messages['meta_keywords.max'] = 'Meta keywords cannot exceed 1000 characters.';
        }

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'start_date' => 'start date',
            'end_date' => 'end date',
        ];

        // Add conditional attributes only if columns exist
        if ($this->hasColumn('client_id')) {
            $attributes['client_id'] = 'client';
        }

        if ($this->hasColumn('client_name')) {
            $attributes['client_name'] = 'client name';
        }

        if ($this->hasColumn('category_id')) {
            $attributes['category_id'] = 'category';
        }

        if ($this->hasColumn('service_id')) {
            $attributes['service_id'] = 'service';
        }

        if ($this->hasColumn('quotation_id')) {
            $attributes['quotation_id'] = 'quotation';
        }

        if ($this->hasColumn('progress_percentage')) {
            $attributes['progress_percentage'] = 'progress percentage';
        }

        if ($this->hasColumn('is_active')) {
            $attributes['is_active'] = 'active status';
        }

        if ($this->hasColumn('display_order')) {
            $attributes['display_order'] = 'display order';
        }

        if ($this->hasColumn('estimated_completion_date')) {
            $attributes['estimated_completion_date'] = 'estimated completion date';
        }

        if ($this->hasColumn('actual_completion_date')) {
            $attributes['actual_completion_date'] = 'actual completion date';
        }

        if ($this->hasColumn('actual_cost')) {
            $attributes['actual_cost'] = 'actual cost';
        }

        if ($this->hasColumn('client_feedback')) {
            $attributes['client_feedback'] = 'client feedback';
        }

        if ($this->hasColumn('lessons_learned')) {
            $attributes['lessons_learned'] = 'lessons learned';
        }

        if ($this->hasColumn('meta_title')) {
            $attributes['meta_title'] = 'meta title';
        }

        if ($this->hasColumn('meta_description')) {
            $attributes['meta_description'] = 'meta description';
        }

        if ($this->hasColumn('meta_keywords')) {
            $attributes['meta_keywords'] = 'meta keywords';
        }

        return $attributes;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Handle nullable fields
        $nullableFields = [
            'slug',
            'location',
            'year',
            'start_date',
            'end_date',
            'challenge',
            'solution',
            'result',
            'value'
        ];

        // Add conditional fields if they exist
        $conditionalFields = [
            'short_description',
            'client_id',
            'service_id',
            'quotation_id',
            'estimated_completion_date',
            'actual_completion_date',
            'budget',
            'actual_cost',
            'client_feedback',
            'lessons_learned',
            'meta_title',
            'meta_description',
            'meta_keywords'
        ];

        foreach ($conditionalFields as $field) {
            if ($this->hasColumn($field)) {
                $nullableFields[] = $field;
            }
        }

        // Convert empty strings to null
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $input) && $input[$field] === '') {
                $input[$field] = null;
            }
        }

        // Handle boolean fields
        $input['featured'] = $this->boolean('featured', false);
        if ($this->hasColumn('is_active')) {
            $input['is_active'] = $this->boolean('is_active', true);
        }

        $this->replace($input);
    }

    /**
     * Clean array field data while preserving individual field values
     */
    private function cleanArrayField($fieldData)
    {
        if (!is_array($fieldData)) {
            return [];
        }

        $cleaned = [];

        foreach ($fieldData as $item) {
            // Handle string values
            if (is_string($item)) {
                $trimmed = trim($item);
                if ($trimmed !== '') {
                    $cleaned[] = $trimmed;
                }
            }
            // Handle numeric values
            elseif (is_numeric($item)) {
                $cleaned[] = (string) $item;
            }
            // Handle nested arrays (shouldn't happen in normal form submission)
            elseif (is_array($item)) {
                $nestedCleaned = $this->cleanArrayField($item);
                $cleaned = array_merge($cleaned, $nestedCleaned);
            }
            // Handle other types
            elseif (!is_null($item)) {
                $stringValue = trim((string) $item);
                if ($stringValue !== '') {
                    $cleaned[] = $stringValue;
                }
            }
        }

        // Return clean array with sequential indexes
        return array_values($cleaned);
    }

    /**
     * Handle additional validation for business rules.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $project = $this->route('project');

            // Check if project can be marked as completed (only validate if milestones exist)
            if ($this->input('status') === 'completed' && $project && method_exists($project, 'milestones')) {
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

            // Validate budget vs actual cost relationship (only if both columns exist)
            if (
                $this->hasColumn('budget') && $this->hasColumn('actual_cost') &&
                $this->filled('budget') && $this->filled('actual_cost')
            ) {
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

            // Validate category is required only if column exists
            if ($this->hasColumn('category_id') && !$this->filled('category_id')) {
                $validator->errors()->add(
                    'category_id',
                    'Project category is required.'
                );
            }

            // Validate priority is required only if column exists
            if ($this->hasColumn('priority') && !$this->filled('priority')) {
                $validator->errors()->add(
                    'priority',
                    'Project priority is required.'
                );
            }
        });
    }
}