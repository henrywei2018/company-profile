<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('edit projects');
    }

    public function rules(): array
    {
        $projectId = $this->route('project')->id;

        return [
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($projectId)],
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'client_id' => 'nullable|exists:users,id',
            'client_name' => 'required_without:client_id|string|max:255',
            'category_id' => 'required|exists:project_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'status' => ['required', Rule::in(['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_completion_date' => 'nullable|date',
            'actual_completion_date' => 'nullable|date',
            'value' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0|max:999999999999.99',
            'actual_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'result' => 'nullable|string', // <-- Ensure this matches your DB field
            'client_feedback' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
            'services_used' => 'nullable|array',
            'services_used.*' => 'string|max:255',
            'technologies_used' => 'nullable|array',
            'technologies_used.*' => 'string|max:255',
            'team_members' => 'nullable|array',
            'team_members.*' => 'string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
            'image_alt_texts' => 'nullable|array',
            'image_alt_texts.*' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required.',
            'slug.unique' => 'This slug is already taken.',
            'description.required' => 'Project description is required.',
            'client_name.required_without' => 'Client name is required when no client is selected.',
            'category_id.required' => 'Project category is required.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'budget.numeric' => 'Budget must be numeric.',
            'budget.min' => 'Budget cannot be negative.',
            'actual_cost.numeric' => 'Actual cost must be numeric.',
            'actual_cost.min' => 'Actual cost cannot be negative.',
            'images.max' => 'Max 10 images allowed.',
            'images.*.mimes' => 'Images must be jpeg, jpg, png, or webp.',
            'images.*.max' => 'Each image must be under 2MB.',
        ];
    }

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

    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Normalize empty strings to null
        foreach ([
            'slug', 'short_description', 'location', 'year', 'client_id', 'service_id', 
            'quotation_id', 'start_date', 'end_date', 'estimated_completion_date', 
            'actual_completion_date', 'value', 'budget', 'actual_cost', 'challenge', 
            'solution', 'result', 'client_feedback', 'lessons_learned', 'meta_title', 
            'meta_description', 'meta_keywords'
        ] as $field) {
            if (array_key_exists($field, $input) && $input[$field] === '') {
                $input[$field] = null;
            }
        }

        // Cast booleans
        $input['featured'] = $this->boolean('featured', false);
        $input['is_active'] = $this->boolean('is_active', true);

        // Clean up array fields
        foreach (['services_used', 'technologies_used', 'team_members'] as $arrayField) {
            if (isset($input[$arrayField]) && is_array($input[$arrayField])) {
                $input[$arrayField] = array_filter($input[$arrayField], fn($v) => trim($v) !== '');
            }
        }

        $this->replace($input);
    }
}
