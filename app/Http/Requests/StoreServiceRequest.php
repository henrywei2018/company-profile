<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'category_id' => 'nullable|exists:service_categories,id',
            'short_description' => 'nullable|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Service title is required.',
            'title.max' => 'Service title cannot exceed 255 characters.',
            'slug.unique' => 'This URL slug is already taken.',
            'slug.max' => 'URL slug cannot exceed 255 characters.',
            'category_id.exists' => 'Selected category is invalid.',
            'short_description.max' => 'Short description cannot exceed 255 characters.',
            'description.required' => 'Service description is required.',
            'icon.image' => 'Icon must be a valid image file.',
            'icon.mimes' => 'Icon must be a file of type: jpeg, png, jpg, gif, svg.',
            'icon.max' => 'Icon size cannot exceed 1MB.',
            'image.image' => 'Service image must be a valid image file.',
            'image.mimes' => 'Service image must be a file of type: jpeg, png, jpg, webp.',
            'image.max' => 'Service image size cannot exceed 2MB.',
            'featured.boolean' => 'Featured field must be true or false.',
            'is_active.boolean' => 'Active status must be true or false.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'meta_title.max' => 'Meta title cannot exceed 255 characters.',
            'meta_description.max' => 'Meta description cannot exceed 255 characters.',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'category_id' => 'category',
            'is_active' => 'active status',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert checkbox values to boolean
        $this->merge([
            'featured' => $this->boolean('featured'),
            'is_active' => $this->boolean('is_active', true), // Default to true for new services
        ]);

        // Clean up empty strings to null
        if ($this->category_id === '') {
            $this->merge(['category_id' => null]);
        }

        if ($this->sort_order === '') {
            $this->merge(['sort_order' => 0]);
        }
    }
}