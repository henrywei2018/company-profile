<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('create services');
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
            'slug' => 'nullable|string|unique:services,slug',
            'category_id' => 'nullable|exists:service_categories,id',
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'icon' => 'nullable|image|max:1024', // Max 1MB
            'image' => 'nullable|image|max:2048', // Max 2MB
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
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
        
        // Set default values
        if (!$this->has('sort_order')) {
            $this->merge([
                'sort_order' => 999, // Will be adjusted properly in the model
            ]);
        }
        
        // Make sure boolean fields are properly cast
        $this->merge([
            'featured' => $this->has('featured') ? true : false,
            'is_active' => $this->has('is_active') ? true : false,
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
            'title.required' => 'Service title is required',
            'description.required' => 'Service description is required',
            'category_id.exists' => 'Selected category does not exist',
            'icon.image' => 'Icon must be an image file',
            'icon.max' => 'Icon size cannot exceed 1MB',
            'image.image' => 'Image must be an image file',
            'image.max' => 'Image size cannot exceed 2MB',
        ];
    }
}