<?php
// File: app/Http/Requests/UpdateProjectMilestoneRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'required|date',
            'completion_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,delayed',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'estimated_hours' => 'nullable|numeric|min:0|max:9999.99',
            'actual_hours' => 'nullable|numeric|min:0|max:9999.99',
            'priority' => 'nullable|in:low,normal,high,critical',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_milestones,id',
            'notes' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Milestone title is required.',
            'title.max' => 'Milestone title cannot exceed 255 characters.',
            'due_date.required' => 'Due date is required.',
            'progress_percent.between' => 'Progress must be between 0 and 100.',
            'estimated_hours.min' => 'Estimated hours must be positive.',
            'actual_hours.min' => 'Actual hours must be positive.',
            'dependencies.*.exists' => 'Selected dependency milestone does not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Auto-set completion date if status is completed and none provided
        if ($this->status === 'completed' && !$this->completion_date) {
            $this->merge(['completion_date' => now()->format('Y-m-d')]);
        }

        // Auto-set progress to 100% if completed and no progress provided
        if ($this->status === 'completed' && !$this->progress_percent) {
            $this->merge(['progress_percent' => 100]);
        }

        // Clear completion date if status is not completed
        if ($this->status !== 'completed' && $this->completion_date) {
            $this->merge(['completion_date' => null]);
        }
    }
}