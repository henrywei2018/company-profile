<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectMilestoneRequest extends FormRequest
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
        $milestone = $this->route('milestone');
        
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,in_progress,completed,delayed',
            'completion_date' => 'nullable|date',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'priority' => 'nullable|in:low,normal,high,critical',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_milestones,id|not_in:' . $milestone->id,
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
            'progress_percent.between' => 'Progress percentage must be between 0 and 100.',
            'dependencies.*.exists' => 'One or more selected dependencies are invalid.',
            'dependencies.*.not_in' => 'A milestone cannot depend on itself.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $milestone = $this->route('milestone');
            
            // Validate status transitions
            if ($milestone->status === 'completed' && $this->status !== 'completed') {
                // Reopening a completed milestone
                if ($this->completion_date) {
                    $validator->errors()->add(
                        'completion_date', 
                        'Completion date should be cleared when reopening a milestone.'
                    );
                }
            }

            // If status is completed, ensure completion date is set
            if ($this->status === 'completed' && !$this->completion_date) {
                // Auto-set completion date
                $this->merge(['completion_date' => now()->format('Y-m-d')]);
            }

            // If completion date is provided, status should be completed
            if ($this->completion_date && $this->status !== 'completed') {
                $validator->errors()->add(
                    'completion_date', 
                    'Completion date can only be set when status is completed.'
                );
            }

            // Validate circular dependencies
            if ($this->dependencies) {
                $this->validateCircularDependencies($validator, $milestone);
            }

            // Validate due date changes for completed milestones
            if ($milestone->status === 'completed' && 
                $milestone->due_date && 
                $this->due_date !== $milestone->due_date->format('Y-m-d')) {
                
                // Allow but warn about changing due date of completed milestone
            }
        });
    }

    /**
     * Validate that dependencies don't create circular references.
     */
    protected function validateCircularDependencies($validator, $milestone)
    {
        $dependencies = $this->dependencies ?? [];
        
        // Simple circular dependency check
        foreach ($dependencies as $dependencyId) {
            if ($this->hasCircularDependency($milestone->id, $dependencyId, $dependencies)) {
                $validator->errors()->add(
                    'dependencies', 
                    'Circular dependency detected. Please check your dependency selections.'
                );
                break;
            }
        }
    }

    /**
     * Check if adding a dependency would create a circular reference.
     */
    protected function hasCircularDependency($milestoneId, $dependencyId, $newDependencies = [])
    {
        // Get existing dependencies for the dependency milestone
        $existingDeps = \App\Models\ProjectMilestone::find($dependencyId)
            ?->dependencies ?? [];
            
        // If the dependency already depends on our milestone, it's circular
        if (in_array($milestoneId, $existingDeps)) {
            return true;
        }
        
        // Check transitive dependencies (one level deep for performance)
        foreach ($existingDeps as $transitiveDepId) {
            $transitiveDeps = \App\Models\ProjectMilestone::find($transitiveDepId)
                ?->dependencies ?? [];
                
            if (in_array($milestoneId, $transitiveDeps)) {
                return true;
            }
        }
        
        return false;
    }
}