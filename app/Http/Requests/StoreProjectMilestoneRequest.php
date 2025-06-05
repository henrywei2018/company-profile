<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectMilestoneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('project'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'required|date|after_or_equal:today',
            'status' => 'required|in:pending,in_progress,completed,delayed',
            'completion_date' => 'nullable|date|before_or_equal:today',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'priority' => 'nullable|in:low,normal,high,critical',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_milestones,id',
            'notes' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The milestone title is required.',
            'title.max' => 'The milestone title may not be greater than 255 characters.',
            'due_date.required' => 'The due date is required.',
            'due_date.after_or_equal' => 'The due date must be today or in the future.',
            'completion_date.before_or_equal' => 'The completion date cannot be in the future.',
            'progress_percent.between' => 'Progress percentage must be between 0 and 100.',
            'dependencies.*.exists' => 'One or more selected dependencies are invalid.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // If status is completed, completion date should be provided
            if ($this->status === 'completed' && !$this->completion_date) {
                // Auto-set completion date to today if not provided
                $this->merge(['completion_date' => now()->format('Y-m-d')]);
            }

            // If completion date is provided, status should be completed
            if ($this->completion_date && $this->status !== 'completed') {
                $validator->errors()->add(
                    'completion_date', 
                    'Completion date can only be set when status is completed.'
                );
            }

            // Validate dependencies don't include self (for updates)
            if ($this->dependencies && $this->route('milestone')) {
                $milestoneId = $this->route('milestone')->id;
                if (in_array($milestoneId, $this->dependencies)) {
                    $validator->errors()->add(
                        'dependencies', 
                        'A milestone cannot depend on itself.'
                    );
                }
            }

            // If progress is 100%, suggest completing the milestone
            if ($this->progress_percent == 100 && $this->status !== 'completed') {
                // This is just a warning, not an error
            }
        });
    }
}