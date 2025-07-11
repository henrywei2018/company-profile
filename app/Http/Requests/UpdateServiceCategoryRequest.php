<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('service_categories')->ignore($this->getRouteModelId()),
            ],
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get the ID of the route model for validation exclusion.
     *
     * @return int|null
     */
    protected function getRouteModelId()
    {
        // Try different possible parameter names for route model binding
        $possibleNames = ['category', 'serviceCategory', 'service_category'];
        
        foreach ($possibleNames as $name) {
            if ($this->route($name)) {
                return $this->route($name)->id;
            }
        }
        
        // Fallback: extract ID from route parameters
        $routeParams = $this->route()->parameters();
        
        // Look for any parameter that looks like a ServiceCategory model
        foreach ($routeParams as $param) {
            if (is_object($param) && method_exists($param, 'getTable') && $param->getTable() === 'service_categories') {
                return $param->id;
            }
        }
        
        return null;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'slug.unique' => 'This URL slug is already taken.',
            'slug.max' => 'URL slug cannot exceed 255 characters.',
            'icon.image' => 'Icon must be a valid image file.',
            'icon.mimes' => 'Icon must be a file of type: jpeg, png, jpg, gif, svg.',
            'icon.max' => 'Icon size cannot exceed 1MB.',
            'is_active.boolean' => 'Active status must be true or false.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be at least 0.',
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
            'is_active' => 'active status',
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
            'is_active' => $this->boolean('is_active'),
        ]);

        // Clean up empty values
        if ($this->sort_order === '') {
            $this->merge(['sort_order' => 0]);
        }
    }
}