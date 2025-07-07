<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_categories')->ignore($this->getRouteModelId()),
            ],
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'parent_id' => [
                'nullable',
                'exists:product_categories,id',
                function ($attribute, $value, $fail) {
                    // Prevent self-referencing
                    if ($value == $this->getRouteModelId()) {
                        $fail('A category cannot be its own parent.');
                    }
                    
                    // Prevent circular references
                    if ($value && $this->wouldCreateCircularReference($value)) {
                        $fail('This parent selection would create a circular reference.');
                    }
                },
            ],
            'service_category_id' => 'nullable|exists:service_categories,id',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get the ID of the route model for validation exclusion.
     */
    protected function getRouteModelId(): ?int
    {
        // Try different possible parameter names for route model binding
        $possibleNames = ['productCategory', 'product_category', 'category', 'id'];
        
        foreach ($possibleNames as $name) {
            if ($this->route($name)) {
                return $this->route($name)->id ?? $this->route($name);
            }
        }
        
        // Fallback: extract ID from route parameters
        $routeParams = $this->route()->parameters();
        
        // Look for any parameter that looks like a ProductCategory model
        foreach ($routeParams as $param) {
            if (is_object($param) && method_exists($param, 'getTable') && $param->getTable() === 'product_categories') {
                return $param->id;
            }
        }
        
        return null;
    }

    /**
     * Check if the parent assignment would create a circular reference.
     */
    protected function wouldCreateCircularReference($parentId): bool
    {
        $categoryId = $this->getRouteModelId();
        
        if (!$categoryId || !$parentId) {
            return false;
        }
        
        // Get the proposed parent and check if current category is in its ancestor chain
        $parent = \App\Models\ProductCategory::find($parentId);
        
        while ($parent) {
            if ($parent->id == $categoryId) {
                return true; // Circular reference detected
            }
            $parent = $parent->parent;
        }
        
        return false;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'slug.unique' => 'This URL slug is already taken.',
            'slug.max' => 'URL slug cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'icon.image' => 'Icon must be a valid image file.',
            'icon.mimes' => 'Icon must be a file of type: jpeg, png, jpg, gif, svg.',
            'icon.max' => 'Icon size cannot exceed 1MB.',
            'parent_id.exists' => 'Selected parent category is invalid.',
            'service_category_id.exists' => 'Selected service category is invalid.',
            'is_active.boolean' => 'Active status must be true or false.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be at least 0.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'parent_id' => 'parent category',
            'service_category_id' => 'service category',
            'is_active' => 'active status',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox values to boolean
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);

        // Clean up empty values
        if ($this->parent_id === '') {
            $this->merge(['parent_id' => null]);
        }

        if ($this->service_category_id === '') {
            $this->merge(['service_category_id' => null]);
        }

        if ($this->sort_order === '') {
            $this->merge(['sort_order' => 0]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional custom validation can go here
            
            // Check if trying to set parent to a descendant
            $parentId = $this->input('parent_id');
            $categoryId = $this->getRouteModelId();
            
            if ($parentId && $categoryId) {
                $category = \App\Models\ProductCategory::find($categoryId);
                $proposedParent = \App\Models\ProductCategory::find($parentId);
                
                if ($category && $proposedParent && $proposedParent->isDescendantOf($category)) {
                    $validator->errors()->add('parent_id', 'Cannot set a descendant as parent category.');
                }
            }
        });
    }
}