<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create projects');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:projects,slug',
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
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:5120', // Max 5MB per image
            'alt_text' => 'nullable|array',
            'alt_text.*' => 'nullable|string|max:255',
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
            'images.required' => 'At least one project image is required',
            'images.min' => 'At least one project image is required',
            'images.*.image' => 'Files must be images',
            'images.*.max' => 'Image size cannot exceed 5MB',
        ];
    }
}