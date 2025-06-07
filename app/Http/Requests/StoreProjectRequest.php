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

    public function rules(): array
    {
        $rules = [
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
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
            'image_alt_texts' => 'nullable|array',
            'image_alt_texts.*' => 'nullable|string|max:255',
        ];

        $additionalRules = [
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

        foreach ($additionalRules as $field => $rule) {
            if ($this->hasColumn($field)) {
                $rules[$field] = $rule;
            }
        }

        return $rules;
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

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            if (
                $this->hasColumn('budget') && $this->hasColumn('actual_cost') &&
                $this->filled('budget') && $this->filled('actual_cost')
            ) {
                $budget = (float) $this->input('budget');
                $actualCost = (float) $this->input('actual_cost');

                if ($actualCost > $budget * 1.5) {
                    $validator->errors()->add('actual_cost', 'Actual cost is significantly over budget. Please review and confirm.');
                }
            }

            if ($this->filled('start_date') && $this->filled('end_date')) {
                $startDate = Carbon::parse($this->input('start_date'));
                $endDate = Carbon::parse($this->input('end_date'));

                if ($endDate->diffInDays($startDate) > 365 * 5) {
                    $validator->errors()->add('end_date', 'Project duration exceeds 5 years. Please verify the dates are correct.');
                }
            }

            if ($this->hasColumn('category_id') && !$this->filled('category_id')) {
                $validator->errors()->add('category_id', 'Project category is required.');
            }

            if ($this->hasColumn('priority') && !$this->filled('priority')) {
                $validator->errors()->add('priority', 'Project priority is required.');
            }
        });
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
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.image' => 'File must be an image.',
            'images.*.mimes' => 'Image must be in JPEG, JPG, PNG, or WebP format.',
            'images.*.max' => 'Image size cannot exceed 2MB.',
        ];
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
            'technologies_used' => 'technologies used',
            'team_members' => 'team members',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }
}
