<?php
// File: app/Http/Requests/StoreProjectRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class StoreProjectRequest extends FormRequest
{
    private function getProjectColumns(): array
    {
        static $columns = null;
        if ($columns === null) {
            $columns = Schema::getColumnListing('projects');
        }
        return $columns;
    }

    private function hasColumn(string $column): bool
    {
        return in_array($column, $this->getProjectColumns());
    }

    public function authorize(): bool
    {
        return auth()->user()->can('create projects');
    }

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

        // Force is_active to boolean (default true if not present)
        $input['featured'] = $this->boolean('featured', false);
        if ($this->hasColumn('is_active')) {
            $input['is_active'] = $this->boolean('is_active', true);
        }

        $this->replace($input);
    }
    public function attributes(): array
    {
        return [
            'end_date' => 'end date',
            'start_date' => 'start date',
            'client_id' => 'client',
            'client_name' => 'client name',
            'category_id' => 'category',
            'service_id' => 'service',
            'quotation_id' => 'quotation',
            'progress_percentage' => 'progress percentage',
            'is_active' => 'active status',
            'display_order' => 'display order',
            'estimated_completion_date' => 'estimated completion date',
            'actual_completion_date' => 'actual completion date',
            'actual_cost' => 'actual cost',
            'client_feedback' => 'client feedback',
            'lessons_learned' => 'lessons learned',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }
    public function rules(): array
    {
        $rules = [
            // Core fields
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug',
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
            
            // Universal Uploader fields - CORRECTED
            // temp_images is NOT sent by universal uploader
            // Universal uploader handles its own validation and temp storage
            
            // Traditional image upload (fallback only)
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
            'image_alt_texts' => 'nullable|array',
            'image_alt_texts.*' => 'nullable|string|max:255',
        ];

        // Add conditional rules only if columns exist
        $conditionalRules = [
            'short_description' => 'nullable|string|max:500',
            'client_id' => 'nullable|exists:users,id',
            'client_name' => 'required_without:client_id|string|max:255',
            'category_id' => 'required|exists:project_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'estimated_completion_date' => 'nullable|date',
            'actual_completion_date' => 'nullable|date',
            'value' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0|max:999999999999.99',
            'actual_cost' => 'nullable|numeric|min:0|max:999999999999.99',
            'client_feedback' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:1000',
        ];

        foreach ($conditionalRules as $field => $rule) {
            if ($this->hasColumn($field)) {
                $rules[$field] = $rule;
            }
        }

        return $rules;
    }
    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required.',
            'title.max' => 'Project title cannot exceed 255 characters.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'description.required' => 'Project description is required.',
            'status.required' => 'Project status is required.',
            'status.in' => 'Invalid project status selected.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            
            // Traditional upload messages only
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.image' => 'File must be an image.',
            'images.*.mimes' => 'Image must be in JPEG, JPG, PNG, or WebP format.',
            'images.*.max' => 'Image size cannot exceed 5MB.',
            'image_alt_texts.*.max' => 'Alt text cannot exceed 255 characters.',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $project = $this->route('project');

            // Validate featured image belongs to project
            if ($this->filled('featured_image_id')) {
                $imageExists = $project->images()
                    ->where('id', $this->input('featured_image_id'))
                    ->exists();

                if (!$imageExists) {
                    $validator->errors()->add('featured_image_id', 'Selected featured image does not belong to this project.');
                }
            }

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

            // Validate date consistency
            if ($this->filled('start_date') && $this->filled('end_date')) {
                $startDate = Carbon::parse($this->input('start_date'));
                $endDate = Carbon::parse($this->input('end_date'));

                if ($endDate->lt($startDate)) {
                    $validator->errors()->add('end_date', 'End date must be after or equal to start date.');
                }
            }
        });
    }
}