<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('update', $this->route('project'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $project = $this->route('project');
        
        return [
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('projects')->ignore($project->id)],
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'client_id' => 'nullable|exists:users,id',
            'client_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'required|string|in:planning,in_progress,completed,on_hold,cancelled',
            'value' => 'nullable|string|max:255',
            'featured' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'result' => 'nullable|string',
            'services_used' => 'nullable|array',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'exists:project_images,id',
            'existing_alt_text' => 'nullable|array',
            'existing_alt_text.*' => 'nullable|string|max:255',
            'featured_image' => 'nullable|exists:project_images,id',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|max:5120', // Max 5MB per image
            'new_alt_text' => 'nullable|array',
            'new_alt_text.*' => 'nullable|string|max:255',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Generate slug from title if not provided
        if (empty($this->slug)) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }
        
        // Make sure boolean fields are properly cast
        $this->merge([
            'featured' => $this->has('featured') ? true : false,
        ]);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Project title is required',
            'description.required' => 'Project description is required',
            'category.required' => 'Project category is required',
            'year.required' => 'Project year is required',
            'year.integer' => 'Project year must be a valid year',
            'year.min' => 'Project year must be 1900 or later',
            'year.max' => 'Project year cannot be in the future',
            'status.required' => 'Project status is required',
            'status.in' => 'Project status must be valid',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
            'existing_images.*.exists' => 'One or more selected images do not exist',
            'featured_image.exists' => 'The selected featured image does not exist',
            'new_images.*.image' => 'Files must be images',
            'new_images.*.max' => 'Image size cannot exceed 5MB',
        ];
    }
}