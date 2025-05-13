<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('projects')->ignore($this->project),
            ],
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'client_id' => 'nullable|exists:users,id',
            'client_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'required|in:planning,in_progress,completed,on_hold,cancelled',
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
            'existing_alt_text.*' => 'string|max:255',
            'featured_image' => 'nullable|exists:project_images,id',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|max:5120', // 5MB max per image
            'new_alt_text' => 'nullable|array',
            'new_alt_text.*' => 'string|max:255',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
        ];
    }
}