<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductCategoryRequest extends FormRequest
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
            'slug' => 'nullable|string|max:255|unique:product_categories,slug',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'parent_id' => [
                'nullable',
                'exists:product_categories,id',
                function ($attribute, $value, $fail) {
                    // Prevent circular references at creation time
                    if ($value) {
                        $parent = \App\Models\ProductCategory::find($value);
                        if ($parent && $parent->parent_id) {
                            // For now, only allow 2 levels deep
                            $fail('Categories can only be nested 2 levels deep.');
                        }
                    }
                },
            ],
            'service_category_id' => 'nullable|exists:service_categories,id',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
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
        // Clean up empty values
        if ($this->parent_id === '') {
            $this->merge(['parent_id' => null]);
        }

        if ($this->service_category_id === '') {
            $this->merge(['service_category_id' => null]);
        }

        // Convert sort_order to integer if provided
        if ($this->sort_order !== null && $this->sort_order !== '') {
            $this->merge(['sort_order' => (int) $this->sort_order]);
        }
    }
}